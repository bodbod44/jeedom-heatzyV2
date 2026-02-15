# This file is part of Jeedom.
#
# Jeedom is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Jeedom is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Jeedom. If not, see <http://www.gnu.org/licenses/>.

import logging
import sys
import os
import time
import traceback
import signal
import json
import argparse
import websocket
from threading import Thread
#import threading

from jeedom.jeedom import jeedom_socket, jeedom_utils, jeedom_com, JEEDOM_SOCKET_MESSAGE  # jeedom_serial


def listen():
	my_jeedom_socket.open()
	try:
		while 1:
			time.sleep(0.5)
			read_socket()
	except KeyboardInterrupt:
		logging.error('Listen KeyboardInterrupt: %s', 'xxx')
		shutdown()

def read_socket():
	if not JEEDOM_SOCKET_MESSAGE.empty():
		logging.debug("Message received in socket JEEDOM_SOCKET_MESSAGE")
		#LeGet = JEEDOM_SOCKET_MESSAGE.get()
		#logging.error("message recu : %s" , LeGet )
		#logging.error("Lestripped...")
		#Lestripped = jeedom_utils.stripped(LeGet)
		logging.debug("loads...")
		#message = json.loads( LeGet )
		message = json.loads(JEEDOM_SOCKET_MESSAGE.get().decode('utf-8'))
		#logging.debug("message recu : %s" , message['apikey'])
		if message['apikey'] != _apikey:
			logging.debug("Invalid apikey from socket: %s", message)
			return
		if message['apikey'] == _apikey:
			logging.debug("OK: %s", message)
			my_jeedom_com.send_change_immediate({'key1' : 'value1', 'key3' : 'value3' })
			logging.debug("Send OK: %s", message)
			#_websocket.send('totototo') # start listening events
			return
		try:
			print('read')
		except Exception as e:
			logging.error('Send command to demon error: %s', e)

def send_socket( mess ):
	def Send_to_jeedom(*args):
		logging.debug('Send_to_jeedom : ' + mess)
		my_jeedom_com.send_change_immediate( mess )
	Thread(target=Send_to_jeedom).start()

def handler(signum=None, frame=None):
	logging.debug("Signal %i caught, exiting...", int(signum))
	shutdown()


def shutdown():
	logging.debug("Shutdown")
	logging.debug("Removing PID file %s", _pidfile)

	try:
		os.remove(_pidfile)
		logging.debug('shutdown - os.remove OK')
	except Exception as e:
		logging.error('Error removing PID file (os.remove) : %s', e)

	try:
		my_jeedom_socket.close()
		logging.debug('shutdown - socket.close OK')
	except Exception as e:
		logging.error('Error closing my_jeedom_socket: %s', e)

	# try:  # if you need jeedom_serial
	#     my_jeedom_serial.close()
	# except Exception as e:
	#     logging.warning('Error closing serial: %s', e)

	try:
		# Close websocket
		_websocket.close()
		logging.debug('shutdown - websocket.close OK')
	except Exception as e:
		logging.warning('Error closing _websocket: %s', e)

	
	logging.debug("Exit 0")
	sys.stdout.flush()
	os._exit(0)

def start_websocket():
	logging.debug("Starting websocket with the container")
	websocket.enableTrace(False)
	host = "ws://" + _ws_url + ":" + _ws_port + "/ws/app/v1"

	_ws = websocket.WebSocketApp(host,
                                on_open = on_open,
                                on_message=on_message_gizwits,
                                on_error=on_error,
                                on_close=on_close)
	#_ws.run_forever()
	#th = threading.Thread(target=_ws.run_forever)
	#th.daemon = True
	#th.start()
	#Thread(target=_ws.run_forever, daemon=True , kwargs={'ping_interval': 30, 'ping_timeout' : 2}).start()
	Thread(target=_ws.run_forever, daemon=True ).start()
	logging.info("Websocket started")
	
# lecture du websocket
def on_message_gizwits(ws, msg):
	global _heartbeat
	_heartbeat = time.time()
	#logging.debug('on_message_gizwits_heartbeat : ' + str(_heartbeat) + ' - ' + str(time.time() - _heartbeat) + ' - ' + time.ctime(_heartbeat) )
	#logging.debug('on_message_gizwits: ' + msg)
	jsonMsg = json.loads(msg)

	#vraimess = '{"cmd":"s2c_noti","data":{"did":"yCpPJifDjBpjNdhsrGEmdH","attrs":{"temp_set_step":"off","mode":"stop","window_switch":false,"p1_data1":85,"p1_data2":85,"p1_data3":85,"p1_data4":85,"p1_data5":85,"p1_data6":85,"p1_data7":85,"p1_data8":85,"p1_data9":85,"p1_data10":85,"p1_data11":5,"p1_data12":85,"p2_data1":85,"p2_data2":85,"p2_data3":85,"p2_data4":85,"p2_data5":85,"p2_data6":85,"p2_data7":85,"p2_data8":85,"p2_data9":85,"p2_data10":85,"p2_data11":85,"p2_data12":85,"p3_data1":85,"p3_data2":85,"p3_data3":85,"p3_data4":85,"p3_data5":85,"p3_data6":85,"p3_data7":101,"p3_data8":85,"p3_data9":85,"p3_data10":85,"p3_data11":85,"p3_data12":85,"p4_data1":85,"p4_data2":85,"p4_data3":85,"p4_data4":85,"p4_data5":85,"p4_data6":85,"p4_data7":85,"p4_data8":85,"p4_data9":85,"p4_data10":85,"p4_data11":85,"p4_data12":85,"p5_data1":85,"p5_data2":85,"p5_data3":85,"p5_data4":85,"p5_data5":85,"p5_data6":85,"p5_data7":85,"p5_data8":85,"p5_data9":85,"p5_data10":85,"p5_data11":85,"p5_data12":85,"p6_data1":85,"p6_data2":85,"p6_data3":85,"p6_data4":85,"p6_data5":85,"p6_data6":85,"p6_data7":85,"p6_data8":85,"p6_data9":85,"p6_data10":85,"p6_data11":85,"p6_data12":85,"p7_data1":85,"p7_data2":85,"p7_data3":85,"p7_data4":85,"p7_data5":85,"p7_data6":85,"p7_data7":85,"p7_data8":85,"p7_data9":85,"p7_data10":85,"p7_data11":85,"p7_data12":85,"derog_mode":0,"derog_time":0,"lock_switch":0,"time_week":7,"time_hour":32,"time_min":0,"timer_switch":0,"boost_switch":0,"boost_time":0,"data1":0,"data2":0,"com_temp":50,"cft_temp":210,"eco_temp":150,"cur_signal":"stop","cur_mode":"stop","Heating_state":false,"cur_humi":84,"cur_temp":85}}}'
	#tvraimess = json.loads(vraimess)
	
	if jsonMsg['cmd'] == 'login_res' and 'success' in jsonMsg['data']:
		if jsonMsg['data']['success'] == True :
			logging.debug('on_message_gizwits - Login OK : ' + msg)
			#send_socket( vraimess )
		else:
			logging.debug('on_message_gizwits - ERROR Login KO : ' + msg)
	elif jsonMsg['cmd'] == 's2c_noti':
		#{"cmd":"s2c_noti","data":{"did":"yCpPJifDjBpjNdhsrGEmdH","attrs":{"temp_set_step":"off","mod
		logging.debug('on_message_gizwits - message notification reçu : ' + msg)
		if 'did' in jsonMsg['data'] and 'attrs' in jsonMsg['data']:
			logging.debug('on_message_gizwits - IL FAUT ENVOYER LES ATTRS AU PLUGIN')
			send_socket( msg )
	elif jsonMsg['cmd'] == 's2c_invalid_msg' and jsonMsg['data']['error_code'] > 0:
		logging.debug('on_message_gizwits - ERROR : ' + str(jsonMsg['data']['error_code']) + ' - ' + jsonMsg['data']['msg'])
		logging.debug('on_message_gizwits - '+ 'keep_running = ' + str(_websocket.keep_running) )
	elif jsonMsg['cmd'] == 'pong':
		logging.debug('on_message_gizwits - PING OK - ' + msg)
	else:
		logging.debug('on_message_gizwits - message non connu : ' + msg)

	#if jsonMsg['type'] == 'version':
		#parseVersionMessage(jsonMsg)

	#if jsonMsg['type'] == 'result':
		#parseResultMessage(jsonMsg)
	
	#if jsonMsg['type'] == 'event':
		#parseEventMessage(jsonMsg)

def on_error(ws, error):
	logging.error('on_error: '+ str(error) )
	logging.debug('on_error: '+ 'keep_running = ' + str(_websocket.keep_running) )

def on_close(ws, close_status_code, close_msg):
	logging.debug('on_close: '+ "Websocket closed")
	logging.debug('on_close: '+ 'keep_running = ' + str(_websocket.keep_running) )

def on_open(ws):
	global _websocket
	_websocket = ws
	logging.debug('on_open: '+ "Websocket opened")
	logging.debug('on_open: '+ 'keep_running = ' + str(_websocket.keep_running) )
    
	message = '{"cmd": "login_req", "data": { "appid": "' + _heatzy_appid + '", "uid": "' + _heatzy_uid + '", "token": "' + _heatzy_token + '", "p0_type": "attrs_v4", "heartbeat_interval": 180 , "auto_subscribe": true }}'
	send_message_gizwitz(message)
	#def run(*args):
		
		#logging.debug("message:" + message)
		#_websocket.send(message) # Set logs
		#ws.send("Hello, Server")
		#logging.debug(_websocket.recv() )
		#time.sleep(1)
		#_websocket.send("{\"command\": \"start_listening\"}") # start listening events
		#time.sleep(1)
		#_websocket.send("{\"command\": \"set_api_schema\", \"schemaVersion\": 16}") # Set API schema
		#time.sleep(1)

	def ping(*args):
		logging.debug('ping: '+ 'keep_running = ' + str(_websocket.keep_running) )
		while _websocket.keep_running:
			if (time.time() - _heartbeat) > 60:
				#logging.debug('_heartbeat : ' + str(_heartbeat) + ' - ' + str(time.time() - _heartbeat) + ' - ' + time.ctime(_heartbeat) + ' - ' + str( (time.time() - _heartbeat) > 60 ) )
				send_message_gizwitz( '{"cmd": "ping"}' )
			time.sleep(10)
	Thread(target=ping).start()

	logging.debug('on_open: '+ "Websocket opened FIN")
	#Thread(target=run).start()
	#run()

def send_message_gizwitz( mess ):
	def SendMessage(*args):
		logging.debug('send_message_gizwitz' + mess)
		_websocket.send(mess)
		#_heartbeat = time.time()
	Thread(target=SendMessage).start()


_log_level = 'debug'
_socket_port = 55099
_socket_host = 'localhost'
_device = 'auto'
_pidfile = '/tmp/heatzyd.pid'
_apikey = ''
_callback = ''
_cycle = 0.3
_ws_url = 'eusandbox.gizwits.com'
_ws_port = '8080'
_heartbeat = time.time()

parser = argparse.ArgumentParser(description='Desmond Daemon for Jeedom plugin')
parser.add_argument("--device", help="Device", type=str)
parser.add_argument("--loglevel", help="Log Level for the daemon", type=str)
parser.add_argument("--callback", help="Callback", type=str)
parser.add_argument("--apikey", help="Apikey", type=str)
parser.add_argument("--cycle", help="Cycle to send event", type=float)
parser.add_argument("--pid", help="Pid file", type=str)
parser.add_argument("--socketport", help="Port for socket server", type=str)
parser.add_argument("--appid", help="App id heatzy", type=str)
parser.add_argument("--token", help="token heatzy", type=str)
parser.add_argument("--uid", help="uid heatzy", type=str)
args = parser.parse_args()

if args.device:
	_device = args.device
if args.loglevel:
	_log_level = args.loglevel
if args.callback:
	_callback = args.callback
if args.apikey:
	_apikey = args.apikey
if args.pid:
	_pidfile = args.pid
if args.cycle:
	_cycle = float(args.cycle)
if args.socketport:
	_socket_port = args.socketport
if args.appid:
	_heatzy_appid = args.appid
if args.token:
	_heatzy_token = args.token
if args.appid:
	_heatzy_uid = args.uid

_socket_port = int(_socket_port)

jeedom_utils.set_log_level(_log_level)

logging.info('Start demond %s' , time.ctime())
logging.info('Start demond %s' , time.time())
logging.info('Start demond %s' , time.ctime(time.time()))
logging.info('Log level: %s', _log_level)
logging.info('Socket port: %s', _socket_port)
logging.info('Socket host: %s', _socket_host)
logging.info('PID file: %s', _pidfile)
logging.info('Apikey: %s', _apikey)
logging.info('Device: %s', _device)

signal.signal(signal.SIGINT, handler)
signal.signal(signal.SIGTERM, handler)

try:
	jeedom_utils.write_pid(str(_pidfile))
	my_jeedom_com = jeedom_com(apikey=_apikey, url=_callback, cycle=_cycle)
	if not my_jeedom_com.test():
		logging.error('Network communication issues. Please fixe your Jeedom network configuration.')
		shutdown()
	# my_jeedom_serial = jeedom_serial(device=_device)  # if you need jeedom_serial
	my_jeedom_socket = jeedom_socket(port=_socket_port, address=_socket_host)
    # https://github.com/lxrootard/eufy/blob/322ff841b71cd7db0e901caca8706fa4ce7452ed/resources/eufyd/eufyd.py#L287
	# my_jeedom_com = jeedom_com(_apikey, _callback)
	
	# Start WebSocket connection with the container
	start_websocket()
	
	listen()
except Exception as e:
	#logging.debug('Fatal error')
	logging.error('Fatal error: %s', e)
	logging.info(traceback.format_exc())
	shutdown()