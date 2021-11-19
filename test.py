#!/usr/bin/python3.6
#-*- coding: utf-8 -*-
import os
import pathlib
print("Content-Type: text/html\r\n\r\n")
print("hello world </br>")
x = 260
y = 32
print(x, "<br>")
print(y, "<br>")
print(x*y, "<br>")
print(x+y, "<br>")
for i in range(10):
    print(i)
    print("</br>")
if os.path.exists('.htaccess'):
    print('True')
else:
    print('False')
z="３０９"
print(z)
print(int(z))
zz = int(z)+10000
print(zz)
