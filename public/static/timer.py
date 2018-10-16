import datetime
from threading import Timer


import socket

from public.static.db import Database

db = Database()
shop_id = "1"


def check_ip(shop):
    #获取本机电脑名
    myname = socket.getfqdn(socket.gethostname(  ))
    #获取本机ip
    myaddr = socket.gethostbyname(myname)
    shopinfo = db.get({"name":shop},"shop")
    return myaddr == shopinfo,myaddr


def timer(time, count=0):
    count += 1
    print('[' + datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S') + '] 第' + str(count) + '次运行@批注的ItemCF算法')
    bl,ip = check_ip()
    if(bl):
        db.update({"ID":shop_id},{"ip_addresss":ip},"shop")
    
    Timer(time, timer, (time, count,)).start()


if __name__ == '__main__':
    timer(5)
