#from smtplib import SMTP_SSL as SMTP
import os
import sys
from smtplib import SMTP
#from email.MIMEText import MIMEText
from email.mime.text import MIMEText
import argparse
import requests
from datetime import datetime
#parser = argparse.ArgumentParser()
#parser.add_argument('-s', action='store', dest='subject_value',
# help='Aportal is down')

#parser.add_argument('-b', action='store', dest='body_value',
# help='Aportal is not reachable')

#parser.add_argument('-r', action='store', dest='recipient_value',
# help='abdallahilyas97@gmail.com')
#result = parser.parse_args()

#print (result.recipient_value)

def alert():
    list_of_sites=[]
    dict_of_site={}
    dict_of_site['url']="https://aportal.584wed.com"
    dict_of_site['name']="Aportal"
    list_of_sites.append(dict_of_site)
    dict_of_site={}
    dict_of_site['url']="https://buyformeretail.com"
    dict_of_site['name']="BFMR"
    list_of_sites.append(dict_of_site)

    for site in list_of_sites:
        request_status_code=requests.get(site['url'])
        request_status_code=request_status_code.status_code
        if request_status_code != 200:
            smtp_server = 'email-smtp.us-east-1.amazonaws.com'
            smtp_port = 587
            #smtp_port = 25
            user = 'AKIAZV3EJ5OOLLBMRJMS'
            password = 'BAQtlszBn/GiLDYNLRA01DKrh/2jtVhQN8CkObnnN9ZB'
            sender = 'Devops <abdullah.projectmarker@nxvt.com>'
            #destination = ["abdallahilyas97@gmail.com","]
            destination = ["abdullah.projectmarker@nxvt.com"]
            text_subtype = 'html'
            time_now=datetime.utcnow()
            time_now = time_now.strftime("%Y-%m-%d %H:%M:%S")
            #content=site['name']+" is working."
            content=site['name'] + " isn't reachable over the web. Please investigate and fix. <br> " + str(time_now) + "<br> Status Code is "+str(request_status_code)
            subject = site['name'] + " Status Changed"
            try:
                msg = MIMEText(content, text_subtype)
                msg['Subject']= subject
                msg['From'] = sender
                msg['To'] = ','.join(destination)

                conn = SMTP(smtp_server, smtp_port)
                conn.set_debuglevel(1)
                conn.ehlo()
                conn.starttls()
                conn.ehlo()
                conn.login(user, password)
                try:
                    if conn.sendmail(sender, destination, msg.as_string()):
                        print("Successful!")
                finally:
                    conn.close()
            except Exception as exc:
                sys.exit("Mail failed; %s" % str(exc))

alert()

