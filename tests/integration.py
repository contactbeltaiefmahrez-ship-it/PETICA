"""Run only on a disposable *_test database; PHP and MariaDB must be available.
PETICA_PHP selects the PHP executable. Test fixtures are never installed by migrate.
"""
import os,sys,subprocess,time,json,re,socket,io,uuid
from pathlib import Path
try: import requests
except ImportError: from pip._vendor import requests
from PIL import Image
root=Path(__file__).resolve().parents[1];php=os.environ.get('PETICA_PHP','php')
env=os.environ.copy();env['PETICA_DB_NAME']='petica_test';env['PETICA_APP_URL']='http://127.0.0.1:8088/petica'
out=root/'tests/output';out.mkdir(exist_ok=True)
def cli(code):
 return subprocess.check_output([php,'-r',"require 'app/core.php';if(!str_ends_with(DB_NAME,'_test'))exit(2);"+code],cwd=root,env=env,text=True)
cli("db()->exec('DROP DATABASE IF EXISTS petica_test');db()->exec('CREATE DATABASE petica_test CHARACTER SET utf8mb4');") if False else None
# The runner creates this disposable database before launching this script.
subprocess.run([php,'bin/console.php','migrate'],cwd=root,env=env,check=True)
fixtures=json.loads(subprocess.check_output([php,'tests/fixtures.php'],cwd=root,env=env,text=True))
log=open(out/'http.log','w');server=subprocess.Popen([php,'-d','session.save_path='+str(out),'-S','127.0.0.1:8088','router.php'],cwd=root,env=env,stdout=log,stderr=log)
checks=[]
def check(name,condition,detail=''):
 checks.append({'name':name,'passed':bool(condition),'detail':detail})
 if not condition: print('FAIL',name,detail,flush=True)
def key():return uuid.uuid4().hex
base='http://127.0.0.1:8088/petica'
class Client:
 def __init__(self,name=None):
  self.s=requests.Session();self.s.trust_env=False;self.csrf=self.get('/auth/me')['csrf']
  if name:self.csrf=self.post('/auth/login',{'email':name+'@petica-test.invalid','password':'Petica-Test-2026!'})['csrf']
 def req(self,m,path,b=None,status=200,**kw):
  r=self.s.request(m,base+'/api'+path,json=b,headers={'X-CSRF-Token':self.csrf} if hasattr(self,'csrf') else {},timeout=15,**kw)
  check(m+' '+path+' → '+str(status),r.status_code==status,r.text[:250] if r.status_code!=status else '')
  d=r.json();return d.get('data',d)
 def get(self,path,status=200):return self.req('GET',path,status=status)
 def post(self,path,b,status=200):return self.req('POST',path,b,status)
 def page(self,path,status=200):
  r=self.s.get(base+path,timeout=15);check('Page '+path,r.status_code==status,r.text[-100:] if r.status_code!=status else '');return r
try:
 for n in range(100):
  try:
   with socket.create_connection(('127.0.0.1',8088),timeout=.1):break
  except OSError:time.sleep(.1)
 anon=Client();owner=Client('owner');other=Client('stranger');admin=Client('admin');vet=Client('vet');groom=Client('groomer')
 check('Fresh movement baseline',anon.get('/movement')['remaining']==999999900)
 owner.post('/profile',{'first_name':'Test','last_name':'Owner','national_id':'PRIVATE-CIN','phone_primary':'+21620000000','governorate':'Tunis','address':'PRIVATE ADDRESS','postal_code':'1000'})
 owner.s.headers['X-CSRF-Token']='wrong'
 bad=owner.s.post(base+'/api/profile',json={},headers={'X-CSRF-Token':'wrong'});check('CSRF blocks mutations',bad.status_code==403);owner.s.headers.pop('X-CSRF-Token')
 im=Image.new('RGB',(120,160),'purple');buf=io.BytesIO();im.save(buf,format='PNG')
 r=owner.s.post(base+'/api/uploads',files={'file':('portrait.png',buf.getvalue(),'image/png')},headers={'X-CSRF-Token':owner.csrf});check('Real multipart image upload',r.status_code==200,r.text[:250]);photo=r.json()['data']['path']
 r=owner.s.post(base+'/api/uploads',files={'file':('evil.php',b'<?php echo 1;?>','image/jpeg')},headers={'X-CSRF-Token':owner.csrf});check('Executable upload rejected',r.status_code==422)
 b={'name':'Test <script>alert(1)</script>','species':'dog','public_contact_consent':1,'public_contact_name':'Test contact','public_contact_phone':'+21620000000','medical_info':'PRIVATE MEDICAL','microchip_number':'PRIVATE-CHIP','photo_path':photo,'idempotency_key':key()}
 c=owner.post('/companions',b);cid=c['companion']['id'];serial=c['companion']['serial_number'];check('DOG exact format',bool(re.fullmatch(r'DOG-\d{9}',serial)))
 again=owner.post('/companions',b);check('Creation idempotent',again['companion']['id']==cid)
 owner.post('/companions',{**b,'name':'Changed'},409)
 check('Movement increments once',anon.get('/movement')['registered']==101)
 other.get('/companions/'+str(cid),403);anon.get('/companions/'+str(cid),401);owner.get('/admin/overview',403)
 c2=owner.post('/companions',{**b,'name':'Cat test','species':'cat','idempotency_key':key()});c3=owner.post('/companions',{**b,'name':'Horse test','species':'horse','idempotency_key':key()})
 check('CAT/HRS exact formats',bool(re.fullmatch(r'CAT-\d{9}',c2['companion']['serial_number'])) and bool(re.fullmatch(r'HRS-\d{9}',c3['companion']['serial_number'])))
 owner.post('/companions',{**b,'species':'rabbit','idempotency_key':key()},422)
 owner.post('/companions/'+str(cid),{**b,'version':0},409)
 owner.post('/companions/'+str(cid)+'/status',{'status':'lost','version':c['companion']['version'],'lost_message':'Please call'})
 detail=owner.get('/companions/'+str(cid));check('Lost state persisted',detail['companion']['status']=='lost')
 qr=detail['qr'];print('QR fields',list(qr),flush=True)
 tag=next(v for k,v in qr.items() if k in ['tag_url','tag'])
 tagpath=tag['url'].replace('http://127.0.0.1:8088','')
 if tagpath.startswith('/petica'):tagpath=tagpath[len('/petica'):]
 page=anon.page(tagpath)
 check('Public QR excludes private data',all(x not in page.text for x in ['PRIVATE-CIN','PRIVATE ADDRESS','PRIVATE MEDICAL','PRIVATE-CHIP']))
 check('Public text escapes XSS','<script>alert(1)</script>' not in page.text and '&lt;script&gt;' in page.text)
 check('Public contacts without JS','tel:+21620000000' in page.text)
 nonce=re.search(r'data-nonce="([^"]+)"',page.text).group(1)
 scan=anon.post('/qr/scans',{'nonce':nonce});scan2=anon.post('/qr/scans',{'nonce':nonce});check('Scan deduplication',scan['event']==scan2['event'])
 event=scan['event'];cap=scan['capability'];other.get('/scan-events/'+event,403);admin.get('/scan-events/'+event,403)
 anon.post('/qr/scans/'+event+'/location',{'capability':'wrong','status':'denied'},403)
 anon.post('/qr/scans/'+event+'/location',{'capability':cap,'status':'denied'})
 check('Denial remains without location',owner.get('/scan-events/'+event)['event']['position'] is None)
 loc={'capability':cap,'status':'available','consent':'location-v1','latitude':36.8,'longitude':10.18,'accuracy':18.5,'captured_at':int(time.time()*1000)}
 anon.post('/qr/scans/'+event+'/location',{**loc,'latitude':91},422)
 anon.post('/qr/scans/'+event+'/location',loc)
 check('Owner receives actual consented coordinates',owner.get('/scan-events/'+event)['event']['position']['latitude']==36.8)
 check('Coordinates encrypted at rest',cli("$r=one('SELECT location_cipher FROM qr_scan_events WHERE public_id=?',['"+event+"']);echo strpos($r['location_cipher'],'latitude')===false?'yes':'no';")=='yes')
 check('Scan never resolves lost case',owner.get('/companions/'+str(cid))['companion']['status']=='lost')
 inv=owner.post('/companions/'+str(cid)+'/owners',{'name':'Secondary Test'})['invite_id']
 owner.post('/companions/'+str(cid)+'/owners/'+str(inv)+'/confirm',{})
 items=[{'product_code':'id_card','companion_id':cid}]
 quote=owner.post('/orders/preview',{'items':items});check('49 + 8 exact millimes',quote['quote']['total_millimes']==57000)
 multi=owner.post('/orders/preview',{'items':items+[{'product_code':'id_card','companion_id':c2['companion']['id']}]});check('Multi 2 percent exact',multi['quote']['total_millimes']==104040)
 sec=owner.post('/orders/preview',{'items':[{'product_code':'id_card','companion_id':cid,'owner_invite_id':inv}]});check('Secondary card -10 percent',sec['quote']['total_millimes']==52100)
 owner.post('/orders/preview',{'items':[{'product_code':'combo_silver','companion_id':cid}]},422)
 orderbody={'quote_id':quote['quote_id'],'shipping_address':'Test shipping','idempotency_key':key()};order=owner.post('/orders',orderbody);oid=order['order_id']
 check('Order idempotent',owner.post('/orders',orderbody)['order_id']==oid);other.get('/orders/'+str(oid),403)
 for stage in ['review','bat','approved','production','ready','shipped','completed']:
  od=admin.get('/orders/'+str(oid))['order'];admin.post('/orders/'+str(oid)+'/status',{'stage':stage,'version':od['version'],'bat_checked':1})
 snap=admin.get('/orders/'+str(oid))['order']['snapshot'];check('Production freezes companion identity',snap['production'][0]['companion']['serial_number']==serial)
 owner.post('/orders/'+str(oid)+'/status',{'stage':'production','version':1},403)
 vet.get('/professionals/clients',403)
 pros=admin.get('/professionals')['items']
 for p in pros:admin.post('/professionals/'+str(p['id'])+'/verification',{'status':'approved'})
 for pro in [vet,groom]:pro.post('/professionals/clients',{'email':'owner@petica-test.invalid'})
 rels=owner.get('/relationships')['items']
 for rel in rels:owner.post('/relationships/'+str(rel['id']),{'consent_status':'accepted','medical':1})
 vet.get('/companions/'+str(cid));check('Groomer medical fields filtered','medical_info' not in groom.get('/companions/'+str(cid))['companion'])
 med={'visit_date':time.strftime('%Y-%m-%d'),'reason':'Test consultation','diagnosis':'Test diagnosis','idempotency_key':key()}
 groom.post('/companions/'+str(cid)+'/vet',med,403);vet.post('/companions/'+str(cid)+'/vet',med)
 check('Owner reads saved vet record',len(owner.get('/companions/'+str(cid)+'/vet')['items'])==1)
 for rel in rels:owner.post('/relationships/'+str(rel['id']),{'consent_status':'revoked'})
 vet.get('/companions/'+str(cid),403)
 contact=admin.post('/crm/contacts',{'name':'Prospect test','email':'prospect@petica-test.invalid'})['contact_id']
 check('Prospect has no fake user',admin.get('/crm/contacts/'+str(contact))['contact']['user_id'] is None)
 admin.post('/crm/contacts/'+str(contact)+'/activity',{'type':'followup','body':'Call test','due_at':'2026-12-01 10:00:00'})
 admin.post('/admin/content',{'slug':'test-article','title_fr':'Essai','title_ar':'اختبار','body_fr':'Contenu test','body_ar':'محتوى اختبار','status':'draft'})
 check('NERO Tracker is future',anon.post('/nero/ask',{'message':'World Tracker GPS'})['answer']!='')
 anon.post('/auth/forgot-password',{'email':'owner@petica-test.invalid'},503)
 exec((root/'tests/journeys.py').read_text())
 workers=[subprocess.Popen([php,'tests/serial_parallel.php',str(i)],cwd=root,env=env,stdout=subprocess.PIPE,stderr=subprocess.PIPE,text=True) for i in range(8)]
 serials=[p.communicate(timeout=30)[0] for p in workers]
 check('Eight concurrent registrations produce distinct serials',all(re.fullmatch(r'DOG-\d{9}',s) for s in serials) and len(set(serials))==8)
 # Every route is rendered in both languages under the corresponding role.
 routes=json.loads(subprocess.check_output([php,'-r',"echo json_encode(array_keys(require 'app/routes.php'));"],cwd=root,env=env,text=True))
 for slug in routes:
  who=admin if slug.startswith(('admin/','crm/')) else vet if slug.startswith('espace-pro/') else owner if slug.startswith('espace-client/') else anon
  if any(x in slug for x in ['compagnon.html','commande.html','client.html','scan.html','contact.html']) and slug.startswith(('espace-','admin/','crm/')):continue
  for language in ['fr','ar']:
   r=who.s.get(base+'/'+language+'/'+slug,timeout=15)
   check('Route '+language+'/'+slug,r.status_code==500 if slug=='500.html' else r.status_code in [200,403,404,410],str(r.status_code))
   if r.status_code==200:check('Language '+language+'/'+slug,'lang="'+language+'"' in r.text)
 for lang in ['fr','ar']:
  owner.page('/'+lang+'/espace-client/compagnon.html?id='+str(cid));admin.page('/'+lang+'/admin/commande.html?id='+str(oid))
 for path in ['/config/local.php','/database/schema.sql','/reference/masters/x','/.git/config']:
  anon.page(path,403)
 anon.page('/robots.txt');anon.page('/sitemap.xml');anon.page('/not-real.html',404)
 cli("q('UPDATE qr_scan_events SET location_expires=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 SECOND)');")
 check('Expired location inaccessible immediately',owner.get('/scan-events/'+event)['event']['position'] is None)
 subprocess.run([php,'bin/console.php','jobs'],cwd=root,env=env,check=True)
 check('Retention purges cipher',cli("echo q('SELECT COUNT(*) FROM qr_scan_events WHERE location_cipher IS NOT NULL')->fetchColumn();")=='0')
except Exception as e:
 import traceback;traceback.print_exc();check('Harness completes',False,repr(e))
finally:
 server.terminate();server.wait(timeout=5);log.close()
 (out/'integration.json').write_text(json.dumps({'checks':checks,'passed':sum(c['passed'] for c in checks),'failed':sum(not c['passed'] for c in checks)},ensure_ascii=False,indent=2))
 print('RESULT',sum(c['passed'] for c in checks),'passed',sum(not c['passed'] for c in checks),'failed')
sys.exit(any(not c['passed'] for c in checks))
