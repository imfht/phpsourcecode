<%
/*
 * Created on 20080406 by wadelau@ufqi.com
 * redesign with SocketString, SocketStream,
 * by Xenxin@ufqi, Mon Jul 30 14:18:37 UTC 2018
 */
%><%@page import="java.net.Socket,java.net.InetSocketAddress"%><%
%><%!

public static class SocketPool{
    
    //- variables, import from invoker
	/*
	   private String longhost ="localhost";
	   private String shorthost ="localhost";
	   private int longport = 8101;
	   private int shortport =8100;
	   private int maxconn=0;
	   private ArrayList socklist=null; 
	   private String appname="session";
	 */

    //- now pool.state=0: in use, pool.state=1: available, ....
    //- @todo socket state: 0:init, 1:available, 2:in user, ... 
    private final static int SOCKET_DEAD = -1;
    private final static int SOCKET_INIT = 0;
    private final static int SOCKET_AVAL = 1;
    private final static int SOCKET_INUSE = 2;

	private static HashMap AppList = new HashMap();
    private final static String Log_Tag = "inc/SocketPool";

    //- constructors

    //- methods, public
	public static PoolParm getList(String LongHost,int LongPort,
                                    String ShortHost,int ShortPort,int MaxConn){
		synchronized(AppList){
			PoolParm poolparm=null;
			poolparm = (PoolParm)AppList.get(LongHost+":"+String.valueOf(LongPort));
			if(poolparm!=null){	
				return poolparm;
			}
			else{
				poolparm = new PoolParm();
				if(LongHost != null && LongPort != 0){
					poolparm.LongHost=LongHost;
					poolparm.LongPort=LongPort;
					poolparm.ShortHost=ShortHost;
					poolparm.ShortPort=ShortPort;
					poolparm.MaxConn=MaxConn;
					poolparm.socklist=new ArrayList();
					AppList.put(LongHost+":"+String.valueOf(LongPort),poolparm);
					debug(Log_Tag + " init getList for "+LongHost+":"+String.valueOf(LongPort));
				}
				else{
					debug(Log_Tag + " init getList with  LongHost or LongPort null");
					return null;
				}
			}
			return poolparm;
		}
	}

    //-
	public static HashMap initSocket(PoolParm poolparm){  
		synchronized(AppList){
			HashMap map = new HashMap();
			try{
				Socket socket = null;
				InetSocketAddress isa = null;
				if(poolparm.socklist.size() < poolparm.MaxConn){
					isa = new InetSocketAddress(poolparm.LongHost, poolparm.LongPort);
					socket = new Socket();
					socket.setTcpNoDelay(false);
					socket.setSendBufferSize(8192);
					socket.setReceiveBufferSize(8192);
					socket.setSoTimeout(5*1000);
					//socket.setTcpNoDelay(true);
					socket.setKeepAlive(true);
					try{
						socket.connect(isa,1000);
					}
					catch(Exception e){
						isa=null;
						socket=null;
						e.printStackTrace();
						return null;
					}
					Pool pool = new Pool();
					pool.socket = socket;
					pool.state=0; //- why 0? @todo: state mgmt
					poolparm.socklist.add(pool);
					map.put("socket",socket);
					map.put("index", poolparm.socklist.indexOf(pool));
					map.put("init","long");
					AppList.remove(poolparm.LongHost+":"+String.valueOf(poolparm.LongPort));
					AppList.put(poolparm.LongHost+":"+String.valueOf(poolparm.LongPort), poolparm);
					debug(Log_Tag + ": init for "+poolparm.LongHost+":"+String.valueOf(poolparm.LongPort)+" long conn-idx:"+map.get("index"));
					pool=null;
				}
				else if(poolparm.ShortHost!=null && poolparm.ShortPort!=0){	
					isa = new InetSocketAddress(poolparm.ShortHost, poolparm.ShortPort);
					socket = new Socket();
					socket.setTcpNoDelay(false);
					socket.setSendBufferSize(8192);
					socket.setReceiveBufferSize(8192);
					socket.setSoTimeout(5*1000);
					//socket.setTcpNoDelay(true);
					try{
						socket.connect(isa,1000);
					}
					catch(Exception e){
						isa=null;
						socket=null;
						e.printStackTrace();
						return null;
					}
					map.put("socket",socket);
					map.put("index", 0); //"short");
					map.put("init","short");
				}
				return map; 
			} 
			catch (Exception e){
				e.printStackTrace();
				map=null;
				return null;
			}
		}
	}

    //-
	public static HashMap getSocket(String LongHost,int LongPort, 
                                    String ShortHost,int ShortPort,int MaxConn){
		synchronized(AppList){
			Socket socket=null;
			Pool pool=null;
			HashMap map=null;
			PoolParm poolparm = getList(LongHost,LongPort,ShortHost,ShortPort,MaxConn);
			if (poolparm == null){
				return null;
			}
			for(int i=0; i<poolparm.socklist.size(); i++){	
				pool = (Pool)poolparm.socklist.get(i);
				if(pool != null){
					if(pool.state != 0){
						socket = pool.socket;
						map = new HashMap();
						map.put("socket",socket);
						map.put("index", String.valueOf(i));
						Pool newpool=new Pool();
						newpool.socket=socket;
						newpool.state=0;
						poolparm.socklist.set(i,newpool);
						AppList.remove(poolparm.LongHost+":"+String.valueOf(poolparm.LongPort));
						AppList.put(poolparm.LongHost+":"+String.valueOf(poolparm.LongPort), poolparm);
						pool=null;
						return map;
					}
					pool=null;
				}
			}
			map = initSocket(poolparm);
			return map;
		}
	}

    //-
	public static HashMap getSocket(String LongHost,int LongPort,int MaxConn){
		return getSocket(LongHost, LongPort, null, 0, MaxConn);
	}

    //-
	public static void setPool(String LongHost,int LongPort,String ShortHost,int ShortPort,
                                int MaxConn,int index,int state){
		synchronized(AppList){
			PoolParm poolparm = getList(LongHost,LongPort,ShortHost,ShortPort,MaxConn);
			if (poolparm == null){
				return ;
			}
			Pool pool=null;
			try	{
				pool=(Pool)poolparm.socklist.get(index);
			}
			catch(Exception ex){
				ex.printStackTrace();
				return;
			}
			try{
				if(pool != null){
					if(state < 0){
						if (pool.socket != null){
							pool.socket.close();
							pool.socket = null;
						}
						poolparm.socklist.remove(index);
						AppList.remove(poolparm.LongHost+":"+String.valueOf(poolparm.LongPort));
						AppList.put(poolparm.LongHost+":"+String.valueOf(poolparm.LongPort), poolparm);
					}
					else if(state == 1){  
						Pool newpool=new Pool();
						newpool.socket=pool.socket;
						newpool.state=1;
						poolparm.socklist.set(index, newpool);
						AppList.remove(poolparm.LongHost+":"+String.valueOf(poolparm.LongPort));
						AppList.put(poolparm.LongHost+":"+String.valueOf(poolparm.LongPort), poolparm);
						newpool=null;
					}
				}
			}
			catch(Exception ex){
				ex.printStackTrace();
			}
		}	
	}

    //-
	public static void setPool(String LongHost,int LongPort,int MaxConn,int index,int state){
		setPool(LongHost, LongPort, null, 0, MaxConn, index, state);
	}

    //-
    public static void hardClose(SocketString sockstr, SocketStream sockstrm){
        //- @todo
        try{
        if(sockstr != null){
            if(sockstr.in != null){ sockstr.in.close(); sockstr.in = null; }
            if(sockstr.out != null){ sockstr.out.close(); sockstr.out = null; }
            if(sockstr.socket != null){ sockstr.socket.close(); sockstr.socket = null; }
        }
        if(sockstrm != null){
            if(sockstrm.in != null){ sockstrm.in.close(); sockstrm.in = null; }
            if(sockstrm.out != null){ sockstrm.out.close(); sockstrm.out = null; }
            if(sockstrm.socket != null){ sockstrm.socket.close(); sockstrm.socket = null; }
        }
        }
        catch(Exception ex){
            ex.printStackTrace();
        }
    }

    //-
	public static void main(String[] args){
		//- @todo
	}


    //----
    //- class Pool
    protected static class Pool{
        public Socket socket=null;
        public int state=0; //-0:in user, 1:available
        //- constructors
        public static void main(String[] args){
            //- @todo
        }
    }

        
    //----
    //- class PoolParm
    protected static class PoolParm{
        public String LongHost=null;
        public int LongPort=0;
        public String ShortHost=null;
        public int ShortPort=0;
        public int MaxConn=0;
        ArrayList socklist=null;
        //- constructors	
        public static void main(String[] args){
            //- @todo
        }
    }

    
    //----
    //- class SocketString,
    //- return a socket-type obj populated with strings
    public static class SocketString{
        private Socket socket = null;
		private PrintWriter out = null;
		private BufferedReader  in = null;
        private int index = 0;
        private String myHost = "";
        private int myPort = 0;
        private int maxConn = 5;
        
        public SocketString(){
            //- @todo
        }
        
        public SocketString(Socket sock){
            try{
            socket = sock;
            out = new PrintWriter(socket.getOutputStream(), true);
            in = new BufferedReader(new InputStreamReader(socket.getInputStream()));
            }
            catch(Exception ex){
                ex.printStackTrace();
            }
        }
		
		public SocketString(HashMap map){
			//- read socket from map from pool
			//Socket sock = (Socket)map.get("socket");
			this((Socket)map.get("socket"));
		}

        public void write(String str){
            out.println(str);
        }
		
        public String readLine(){
            try{
            return in.readLine();
            }
            catch(Exception ex){
                ex.printStackTrace();
            }
            return "";
        }
		
		public void flush(){
			out.flush();
		}
		
		public void close(){
			//- stream close?
			//- socket close?
            if(true){
                //- return to pool with succ state=1
                SocketPool.setPool(this.myHost, this.myPort, "", 0, this.maxConn, this.index, 1);
            }
		}

        public void hardClose(){
            SocketPool.hardClose(this, null);
        }

    }


    //----
    //- class SocketStream
    //- return a socket-type obj populated with bytes
    public static class SocketStream{
		private Socket socket = null;
		private DataInputStream in;
		private BufferedOutputStream out;
        private int index = 0;
        private String myHost = "";
        private int myPort = 0;
        private int maxConn = 5;

		public SocketStream(){
			//- @todo
		}

		public SocketStream(Socket sock){
            //- @todo
            try{
			socket = sock;
			in = new DataInputStream(new BufferedInputStream(sock.getInputStream()));
			out = new BufferedOutputStream(sock.getOutputStream());
            }
            catch(Exception ex){
                ex.printStackTrace();
            }
		}

		public SocketStream(String myHost, int myPort, int maxConn){	
            this.myHost = myHost;
            this.myPort = myPort;
            this.maxConn = maxConn;
            HashMap map = null;
            String init = "";
            int retry=5; int hasTry = 0; boolean issucc = false;
            for(int i=0; i<retry; i++){
                try{
                    map=(HashMap)SocketPool.getSocket(myHost, myPort, maxConn);
                    if(map==null){
                        debug("read from pool failed at i:"+i+" , continue next...");
                        continue;
                    }
                    socket=(Socket)map.get("socket");
                    index=Wht.parseInt(map.get("index"));
                    init=(String)map.get("init");
                    if (init!=null && init.equals("long")){
                        System.out.println("Session init long i:"+i);
                    }
                    map=null;
                    if(socket==null){
                        debug(Log_Tag + ": socket "+i+" is null, try next....");
                        continue;
                    }
                    else if(socket.isClosed()){
                        debug(Log_Tag + ": socket "+i+" is closed already, try next....");
                        continue;
                    }
                    else if(!socket.isConnected()){
                        debug(Log_Tag + ": socket "+i+" is not connected already, try next....");
                        continue;
                    }
                    else{
                        //debug(Log_Tag + ": socket:"+i+" is succ resumed....");
                        issucc = true;
                        break;
                    }
                }
                catch(Exception ex) {
                    ex.printStackTrace();
                    if(socket != null){ 
                        try{
                        socket.close(); socket=null;
                        }
                        catch(Exception ex2){}
                    }
                }
                hasTry = i;
            }
            boolean hasSock = false;
            if(issucc){
                //- @something todo
                hasSock = true;
            }
            else{
                debug(Log_Tag + ": pool failed. try to reinit. 0606092818.");
                if(hasTry >= retry){
                    map = (HashMap)SocketPool.initSocket(SocketPool.getList(myHost, myPort, "", 0, maxConn));
                    socket=(Socket)map.get("socket");
                    index=Wht.parseInt(map.get("index"));
                    init=(String)map.get("init");
                    hasSock = true;
                }
            }
            if(hasSock){
                this.index = index;
                this.socket = socket;
                try{
                in = new DataInputStream(new BufferedInputStream(socket.getInputStream()));
                out = new BufferedOutputStream(socket.getOutputStream());
                }
                catch(Exception ex){
                    ex.printStackTrace();
                }
            }
            else{
                debug(Log_Tag + ": failed all & all. 1808021921.");
            }
        }
		
		public void write(byte[] by){
            try{
			out.write(by);
            }
            catch(Exception ex){
                ex.printStackTrace();
            }
		}
		
		public int read(byte[] by){
			int count = 0; int cnt = 0;
            try{
			while ( count < by.length ) {
				cnt = in.read( by, count, (by.length - count) );
				count += cnt;
			}
            }
            catch(Exception ex){
                ex.printStackTrace();
            }
			return count;
		}
		
        public String readLine(){
            ByteArrayOutputStream bos = new ByteArrayOutputStream();
            byte[] b = new byte[1];
            boolean iseol = false;
            try{
                while(in.read(b, 0, 1) != -1){
                    if(b[0] == 13){ iseol = true;}
                    else{
                        if(iseol){
                            if(b[0] == 10){
                                break;
                            }
                            iseol = false;
                        }
                    }
                    bos.write(b, 0, 1);
                }
                if(bos.size() > 0){
                    //debug("inc/SocketStream: readLine:["+bos.toString()+"]");
                    return bos.toString().trim();
                }
                else{
                    debug("inc/SocketStream: stream seems dead...");
                }
            }
            catch(Exception ex){
                ex.printStackTrace();
            }
            return "";
        }

		public void flush(){
            try{
			out.flush();
            }
            catch(Exception ex){
                ex.printStackTrace();
            }
		}
		
		public void close(){
			//- stream close
			//- socket close?
            if(true){
                //- return to pool with succ state=1
                SocketPool.setPool(this.myHost, this.myPort, "", 0, this.maxConn, this.index, 1);
            }
		}

        public void hardClose(){
            SocketPool.hardClose(null, this);
        }
    }

}

%>
