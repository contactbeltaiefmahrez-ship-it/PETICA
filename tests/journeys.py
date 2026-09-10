# Executed by integration.py with its authenticated HTTP clients and isolated database.
# Real seven-step forms, including multipart and POST/Redirect/GET.
wizard=requests.Session();wizard.trust_env=False
def wizard_post(action,data=None,files=None,expected=200):
 current=wizard.get(base+'/fr/inscription/client.html',timeout=15)
 token=re.search(r'name="csrf-token" content="([^"]+)"',current.text).group(1)
 payload={'_action':action,'_csrf':token,'lang':'fr',**(data or {})}
 response=wizard.post(base+'/fr/inscription/client.html',data=payload,files=files,timeout=15)
 check('Wizard '+action,response.status_code==expected,str(response.status_code))
 return response
wizard_post('/wizard/account',{'email':'wizard@petica-test.invalid','password':'Petica-Wizard-2026!'})
wizard_post('/wizard/owner',{'first_name':'Wizard','last_name':'Test','phone_primary':'20000001','governorate':'Tunis','address':'Test address'})
wizard_post('/wizard/companion',{'name':'Wizard companion','species':'cat','sex':'female','vaccinated':'0'})
wizard_post('/wizard/photo',files={'photo':('portrait.png',buf.getvalue(),'image/png')})
confirmed=wizard_post('/wizard/confirm');check('Wizard confirms persistent CAT identity','CAT-' in confirmed.text and '?step=6' in confirmed.url)
wizard_post('/wizard/confirm')
check('Wizard retry creates one identity',cli("echo q(\"SELECT COUNT(*) FROM companions WHERE name='Wizard companion'\")->fetchColumn();")=='1')
review=wizard_post('/wizard/order',{'product_code':'id_card'})
quote_id=re.search(r'name="quote_id" value="([^"]+)"',review.text).group(1)
wizard_post('/wizard/place-order',{'quote_id':quote_id,'shipping_address':'Test destination','idempotency_key':key()})
check('Wizard reaches real confirmation','?step=7' in wizard.get(base+'/fr/inscription/client.html?step=7').url)
# A refreshed confirmation gets a new key but cannot duplicate the same quote.
replay=owner.post('/orders',{**orderbody,'idempotency_key':key()});check('Quote can produce only one order',replay['order_id']==oid)
# Edit through the actual dossier form: preserve URL, retain both permanent tokens.
before=owner.get('/companions/'+str(cid));edit={**b,'name':'Edited test','version':before['companion']['version'],'_action':'/companions/'+str(cid),'_csrf':owner.csrf}
response=owner.s.post(base+'/fr/espace-client/compagnon.html?id='+str(cid),data=edit,timeout=15)
check('Dossier PRG retains id',response.status_code==200 and 'id='+str(cid) in response.url)
after=owner.get('/companions/'+str(cid));check('Identity and tag tokens permanent across edits',after['qr']==before['qr'])
owner.post('/companions/'+str(cid)+'/status',{'status':'present','version':after['companion']['version']})
check('Owner explicitly resolves lost case',cli("echo q('SELECT COUNT(*) FROM lost_cases WHERE companion_id="+str(cid)+" AND state=\"resolved\"')->fetchColumn();")=='1')
anon.page('/user/PETICAQR/index.php?id='+str(cid)+'&petica='+serial)
anon.page('/user/PETICAQR/index.php?id='+str(cid+1000)+'&petica='+serial,404)
anon.page('/id/'+serial)
anon.page('/q/not-a-token',404)
# Professional registration and exact discount with accepted client scope.
pro_signup=Client();registered=pro_signup.post('/auth/register-professional',{'email':'new-pro@petica-test.invalid','password':'Petica-Pro-Test-2026!','business_name':'Test educator','profession':'trainer','phone':'20000002','city':'Tunis'});pro_signup.csrf=registered['csrf']
check('New pro remains pending',pro_signup.get('/professionals/me')['profile']['verification_status']=='pending')
pro_signup.get('/professionals/clients',403)
for rel in rels:owner.post('/relationships/'+str(rel['id']),{'consent_status':'accepted','medical':1})
pro_quote=vet.post('/orders/preview',{'items':items,'professional_discount':1});check('Professional 10 percent',pro_quote['quote']['total_millimes']==52100)
pro_order=vet.post('/orders',{'quote_id':pro_quote['quote_id'],'shipping_address':'Test pro delivery','idempotency_key':key()})
check('Pro order does not expose production private snapshot','snapshot' not in vet.get('/orders/'+str(pro_order['order_id']))['order'])
# Promo quota, expiry and integer rounding.
admin.post('/admin/promo',{'code':'TEST10','discount_type':'percent','discount_value':10,'usage_limit':1,'per_user_limit':1,'active':1})
promo=owner.post('/orders/preview',{'items':items,'promo_code':'TEST10'});check('Promo recalculates real quote',promo['quote']['total_millimes']==52100)
owner.post('/orders',{'quote_id':promo['quote_id'],'shipping_address':'Test promo','idempotency_key':key()})
owner.post('/orders/preview',{'items':items,'promo_code':'TEST10'},422)
owner.post('/orders/preview',{'items':[{'product_code':'combo','companion_id':cid}]},422)
owner.post('/orders/preview',{'items':[{'product_code':'qr_tag','companion_id':cid,'owner_invite_id':inv}]},422)
check('Three companion discount 4 percent',owner.post('/orders/preview',{'items':items+[{'product_code':'id_card','companion_id':c2['companion']['id']},{'product_code':'id_card','companion_id':c3['companion']['id']}]})['quote']['total_millimes']==149120)
# SQL injection and malformed value should not become an unhandled server error.
other.post('/auth/login',{'email':"' OR 1=1 --",'password':'does-not-match'},401)
anon.post('/contact',{'name':['bad'],'email':'a@b.invalid','message':'Test'},422)
owner.get('/auth/me.php')
owner.get('/companions/detail.php?id='+str(cid))
# Session invalidation must take effect on the next request.
admin.post('/admin/users/'+str(fixtures['stranger'])+'/status',{'status':'suspended'})
other.get('/companions',401)
# A stale quote cannot silently use a new product price.
stale=owner.post('/orders/preview',{'items':items})
product=next(p for p in admin.get('/admin/products')['items'] if p['code']=='id_card')
admin.post('/admin/products/'+str(product['id']),{'name_fr':product['name_fr'],'name_ar':product['name_ar'],'price_tnd':50,'active':1,'commercial_approved':1})
owner.post('/orders',{'quote_id':stale['quote_id'],'shipping_address':'Test','idempotency_key':key()},409)
check('Earlier production price remains immutable',admin.get('/orders/'+str(oid))['order']['total_tnd']=='57.000')
admin.post('/admin/products/'+str(product['id']),{'name_fr':product['name_fr'],'name_ar':product['name_ar'],'price_tnd':49,'active':1,'commercial_approved':1})
