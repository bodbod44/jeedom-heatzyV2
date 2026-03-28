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
		logging.debug("read_socket - Message received in socket JEEDOM_SOCKET_MESSAGE")
		message = json.loads(JEEDOM_SOCKET_MESSAGE.get().decode('utf-8'))
		if 'apikey' in message and 'message' in message:
			if message['apikey'] == _apikey:
				logging.debug("read_socket - message valide : %s", message)
				if message['message']['cmd'] == 'login_req' or message['message']['cmd'] == 'c2s_read' or message['message']['cmd'] == 'c2s_write' :
					ws_gizwitz_send_message( json.dumps( message['message']) )
				elif message['message']['cmd'] == 'stop' :
					logging.debug("read_socket - stop")
					shutdown()
				elif message['message']['cmd'] == 'log_level' :
					logging.debug("read_socket - changement du niveau de log - " + message['message']['log_level'])
					jeedom_utils.set_log_level( message['message']['log_level'] )
				else:
					logging.error("read_socket - Commande non trouvée : %s", message)
			else:
				logging.error("read_socket - apikey invalide: %s", message)
		else:
			logging.error("read_socket - Invalid message: %s", message)


def send_socket( mess ):
	def Send_to_jeedom(*args):
		#logging.debug('Send_to_jeedom : ' + mess)
		my_jeedom_com.send_change_immediate( mess )
	Thread(target=Send_to_jeedom).start()

def handler(signum=None, frame=None):
	logging.debug("Signal %i caught, exiting... (%s)", int(signum) , frame)
	shutdown()


def shutdown():
	logging.debug("shutdown - Removing PID file %s", _pidfile)

	try:
		os.remove(_pidfile)
		logging.debug('shutdown - os.remove OK')
	except Exception as e:
		logging.error('shutdown - Error removing PID file (os.remove) : %s', e)

	try:
		my_jeedom_socket.close()
		logging.debug('shutdown - socket.close OK')
	except Exception as e:
		logging.error('shutdown - Error closing my_jeedom_socket: %s', e)

	# try:  # if you need jeedom_serial
	#     my_jeedom_serial.close()
	# except Exception as e:
	#     logging.warning('Error closing serial: %s', e)

	try:
		# Close websocket
		_websocket.close()
		logging.debug('shutdown - websocket.close OK')
	except Exception as e:
		logging.warning('shutdown - Error closing _websocket: %s', e)

	
	logging.debug("Exit 0")
	sys.stdout.flush()
	os._exit(0)

def start_websocket():
	logging.debug("Starting websocket with the container")
	websocket.enableTrace(False)
	host = _ws_gizwitz_ws + "://" + _ws_gizwitz_url + ":" + _ws_gizwitz_port + "/ws/app/v1"

	_ws = websocket.WebSocketApp(host,
                                on_open    = ws_gizwitz_on_open,
                                on_message = ws_gizwits_on_message,
                                on_error   = ws_gizwits_on_error,
                                on_close   = ws_gizwits_on_close )
	#_ws.run_forever()
	#th = threading.Thread(target=_ws.run_forever)
	#th.daemon = True
	#th.start()
	#Thread(target=_ws.run_forever, daemon=True , kwargs={'ping_interval': 30, 'ping_timeout' : 2}).start()
	Thread(target=_ws.run_forever, daemon=True ).start()
	logging.info("Websocket started")
	
# lecture du websocket (récéptino d'un message gizwits)
def ws_gizwits_on_message(ws, msg):
	global _ws_gizwitz_ws_status
	global _ws_gizwitz_login_status
	global _ws_gizwitz_heartbeat_receive
	#logging.debug('ws_gizwits_on_message...')
	_ws_gizwitz_heartbeat_receive = time.time()
	jsonMsg = json.loads(msg)
	
	# retour du login
	if jsonMsg['cmd'] == 'login_res' and 'success' in jsonMsg['data']:
		if jsonMsg['data']['success'] == True :
			_ws_gizwitz_login_status = 0
			logging.debug('ws_gizwits_on_message - Login OK : ' + msg)
		else:
			_ws_gizwitz_login_status += 1
			logging.debug('ws_gizwits_on_message - ERROR Login KO : ' + msg)
	# notification de changement depuis gizwits
	elif jsonMsg['cmd'] == 's2c_noti' or jsonMsg['cmd'] == 's2c_online_status':
		logging.debug('ws_gizwits_on_message - message notification reçu : ' + msg)
		if 'did' in jsonMsg['data']:
			send_socket( msg )
	# retour d'une erreur (format, login KO ...)
	elif jsonMsg['cmd'] == 's2c_invalid_msg' and jsonMsg['data']['error_code'] > 0:
		if jsonMsg['data']['error_code'] == 1003 or  jsonMsg['data']['error_code'] == 1009:
			_ws_gizwitz_login_status += 1
			logging.debug('ws_gizwits_on_message - '+ 'ws_gizwits login KO please login again status=' + str(_ws_gizwitz_login_status) + ' (' + str(jsonMsg['data']['error_code']) + ') - ' + msg )
			# si login KO, on relance
			ws_gizwitz_send_login()
		elif jsonMsg['data']['error_code'] == 1011:
			_ws_gizwitz_ws_status = False
			logging.debug('ws_gizwits_on_message - ' + 'ws_gizwits websocket déconnecté goodbye (' + str(jsonMsg['data']['error_code']) + ') - ' + msg )
		else:
			logging.debug('ws_gizwits_on_message - ERROR : ' + str(jsonMsg['data']['error_code']) + ' - ' + jsonMsg['data']['msg'] + 'mess:' + msg )
			#logging.debug('ws_gizwits_on_message - '+ 'keep_running = ' + str(_websocket.keep_running) )
	# retour du ping
	elif jsonMsg['cmd'] == 'pong':
		logging.debug('ws_gizwits_on_message - PING OK - ' + msg)
	else:
		logging.error('ws_gizwits_on_message - message non connu : ' + msg)

def ws_gizwits_on_error(ws, error):
	logging.error('ws_gizwits_on_error: error '+ str(error) )

def ws_gizwits_on_close(ws, close_status_code, close_msg):
	logging.debug('ws_gizwits_on_close: ' + 'Websocket closed')
	shutdown()

def ws_gizwitz_on_open(ws):
	global _websocket
	_websocket = ws
	logging.debug('ws_gizwitz_on_open: ' + 'Websocket open...')

	global _ws_gizwitz_ws_status
	_ws_gizwitz_ws_status = True

	ws_gizwitz_send_login()
	
	def ping(*args):
		logging.debug('ping' )
		status_receive = True
		while _websocket.keep_running:
			if (time.time() - _ws_gizwitz_heartbeat_send) > _ws_gizwitz_heartbeat_ping:
				logging.debug('ping: '+ '_ws_gizwitz_ws_status=' + str(_ws_gizwitz_ws_status) )
				ws_gizwitz_send_message( '{"cmd": "ping"}' )
			time.sleep(1)
			if status_receive == True and (time.time() - _ws_gizwitz_heartbeat_receive) > (_ws_gizwitz_heartbeat_ping + 20):
				logging.info('ping: '+ 'Hummm, je ne vois plus de communication' )
				status_receive = False
			if status_receive == False and (time.time() - _ws_gizwitz_heartbeat_receive) < (_ws_gizwitz_heartbeat_ping + 20):
				logging.info('ping: '+ 'Hummm, la connexion semble rétablie' )
				status_receive = True
				
			time.sleep(9)
	Thread(target=ping).start()	
	logging.debug('ws_gizwitz_on_open: '+ "Websocket opened")


def ws_gizwitz_send_message( mess , force = False ):
	global _ws_gizwitz_ws_status
	#global _ws_gizwitz_login_status
	if (not _ws_gizwitz_ws_status or _ws_gizwitz_login_status > 0) and not force:
		if _ws_gizwitz_ws_status and _ws_gizwitz_login_status > 0:
			logging.error('ws_gizwitz_send_message: login KO' )
			logging.error('ws_gizwitz_send_message: nouvelle tentative login' )
			if _ws_gizwitz_login_status < 2:
				ws_gizwitz_send_login()
				time.sleep(2)
		else:
			logging.error('ws_gizwitz_send_message: WebSocket KO' )
			return
	
	global _ws_gizwitz_heartbeat_send
	_ws_gizwitz_heartbeat_send = time.time()
	def SendMessageToGizwits(*args):
		logging.debug('ws_gizwitz_send_message - SendMessageToGizwits: ' + mess)
		_websocket.send(mess)
	Thread(target=SendMessageToGizwits).start()

def ws_gizwitz_send_login():
	message = ( '{"cmd": "login_req",'
				'"data": { "appid": "' + _heatzy_appid + '"'
						', "uid": "'   + _heatzy_uid   + '"'
                    	', "token": "' + _heatzy_token + '"'
                    	', "p0_type": "attrs_v4"'
                    	', "heartbeat_interval": ' + str( _ws_gizwitz_heartbeat_interval ) + ''
						', "auto_subscribe": true }'
				 '}'
				)
	ws_gizwitz_send_message( message , True)

_log_level = 'debug'
_socket_port = 55099
_socket_host = 'localhost'
_device = 'auto'
_pidfile = '/tmp/heatzyd.pid'
_apikey = ''
_callback = ''
_cycle = 0.3
_ws_gizwitz_ws = 'wss'  # ws / wss
_ws_gizwitz_url = 'eusandbox.gizwits.com'
_ws_gizwitz_port = '8880'  # 8080 / 8880
_ws_gizwitz_heartbeat_send = time.time()
_ws_gizwitz_heartbeat_receive = time.time()
_ws_gizwitz_ws_status = False
#_ws_gizwitz_ws_status_receive = False
_ws_gizwitz_login_status = 0
_ws_gizwitz_heartbeat_ping = 60
_ws_gizwitz_heartbeat_interval = 600

parser = argparse.ArgumentParser(description='Desmond Daemon for Jeedom plugin')
parser.add_argument("--device"    , help="Device"                  , type=str)
parser.add_argument("--loglevel"  , help="Log Level for the daemon", type=str)
parser.add_argument("--callback"  , help="Callback"                , type=str)
parser.add_argument("--apikey"    , help="Apikey"                  , type=str)
parser.add_argument("--cycle"     , help="Cycle to send event"     , type=float)
parser.add_argument("--pid"       , help="Pid file"                , type=str)
parser.add_argument("--socketport", help="Port for socket server"  , type=str)
parser.add_argument("--appid"     , help="App id heatzy"           , type=str)
parser.add_argument("--token"     , help="token heatzy"            , type=str)
parser.add_argument("--uid"       , help="uid heatzy"              , type=str)
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
logging.debug('Log level: %s', _log_level)
logging.debug('Socket port: %s', _socket_port)
logging.debug('Socket host: %s', _socket_host)
logging.debug('PID file: %s', _pidfile)
logging.debug('Apikey: %s', _apikey)
logging.debug('Device: %s', _device)

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
	
	# Start WebSocket connection with the container
	start_websocket()
	
	listen()
except Exception as e:
	#logging.debug('Fatal error')
	logging.error('Fatal error: %s', e)
	logging.info(traceback.format_exc())
	shutdown()