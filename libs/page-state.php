<?php
/**
 * Get and record into a array the page-state.
 *
 */
class PageStates {
	public $ALL;

	public $pageName;
	public $pageName_last;

	public $output;
	public $output_mime;
	public $output_xinput;

	public function PageStates() {
		global $default_output_type, $output_types;

		$this->output = empty($_GET['output'])? $default_output_type: $_GET['output'];
		$this->output_mime = $output_types[$this->output];
		$this->output_xinput =  empty($_GET['xinput'])? 0: 1;
		if ($this->output_xinput)
			$this->output_mime = $output_types['xml'];
		$this->pageName = preg_match('/^([^&=]+)(=)?/',@$_SERVER['QUERY_STRING'],$m)? (empty($m[2])? $m[1]: 'index'): 'index';  
			// extract pageName from GET
		$this->pageName_last = empty($_SESSION['last'])? 'index': $_SESSION['last'];
		$_SESSION['last']=$this->pageName;
		$this->refresh_ALL();
	}

	public function refresh_ALL() {
		$this->ALL = array();
		if (count($_POST))
			$this->ALL['_POST'] = $_POST;  // REVISAR: indices [var] podem ser controlados por php ou XML! ver xarray2xstr
			// ver todas as possibilidades de post: form_prePost, uso do XHTMDB, etc.
		$this->ALL['_SESSION'] = $_SESSION;
		$this->ALL['this']=$this->pageName;
		$this->ALL['last']=$this->pageName_last;
	}
}


/**
 * Tratamento de estados: GET, POST, SESSION e SERVER.
 *
 */
class StatesOld {
	// apenas variáveis de estado!
	public $thispage; // nomes publicos daqui ao inves de variaveis globais ... certo passar paa public sisFisio
	public $FORM_SUCESSO;
	public $LOGADO;
	public $LOGADO_tipo;
	public $thispageID;
	public $formID;
	public $OPT;
	public $CMD;
	public $rq_user;

	public $fake_user; // resolve bug do usuario forçado
	public $fake_idp;

	private $sessions;
	private $server;
	private $gets; // controle GET, que vem via REQUEST
	private $publics;
	private $allnames; //public, sessions and gets

	public $all; //all name=>value, SOBREPONDO na precedencia dada por get()


	public function get($name,$type='all') {
		// recupera estado específico, get ou session
		if ( in_array($name,$this->publics)) {
			$TMP = $this->getPubProps(); // COMO fazer direto??
			return $TMP[$name];
		}
		//elseif ( in_array($name,$this->sessions))
		elseif ( array_key_exists($name,$_SESSION) )
			return $_SESSION[$name];

		//elseif ( in_array($name,$this->gets))
		elseif ( array_key_exists($name,$_REQUEST) )
			return $_REQUEST[$name];

		//elseif ( in_array($name,$this->server))
		elseif ( array_key_exists($name,$_SERVER) )
			return $_SERVER[$name];
		else 
			return FALSE;
	}

	public function getAll() {
		// populando $this->all com o ESTADO ATUAL (para uso em XSLT)
		$this->all = array(); // zera

		$TMP=$this->getPubProps();
		unset($TMP['all']);

		foreach ($this->server as $name)
			$this->all[$name] = @$_SERVER[$name];
		foreach ($this->gets as $name)
			$this->all[$name] = @$_REQUEST[$name];
		foreach ($this->sessions as $name)
			$this->all[$name] = @$_SESSION[$name];

		$this->all = array_merge( $this->all, $TMP );  //$this->all + $TMP; //array_merge( $this->all, $this->getPubProps() );

		return true;
	}

	public function setPropFromSes($name, $default='') {
		// analogo a setSesFromGet, mas usa SESSION e poe em prop
		if ( array_key_exists($name,$_REQUEST) )
			return $this->set($name,$_REQUEST[$name],'ses');

		elseif ( array_key_exists($name,$_SESSION) )
			return $this->set($name,$_SESSION[$name],'prp');

		else
			return $this->set($name,$default,'prp'); // alterar anterior (!) e preserva sem session
	}

	public function setSesFromGet($name, $default='') {
		if ( array_key_exists($name,$_REQUEST) )
			return $this->set($name,$_REQUEST[$name],'ses');

		elseif ( array_key_exists($name,$_SESSION) )
			return $_SESSION[$name];

		else
			return $this->set($name,$default,'ses');
	}

	public function set($name,$val,$type='') {
		// types 'get|ses|ser|prp'
		// popula estado especfifico, duplicando em session se preciso

		if (  $type=='ses' ) {
			$_SESSION[$name] = $val;
			array_push($this->sessions,$name); //nao seria mais simples usar todas!?
		} elseif ( in_array($name,$this->sessions) )
			$_SESSION[$name] = $val;
		elseif ( $type=='prp' || in_array($name,$this->publics))  // ? tem metodo PHP melhor que swith para isso?
			switch ($name) {
			    case 'thispage': $this->thispage = $val; break;
			    case 'FORM_SUCESSO': $this->FORM_SUCESSO = $val; break;
			    case 'LOGADO': $this->LOGADO = $val; break;
			    case 'thispageID': $this->thispageID = $val; break;
			    case 'formID': $this->formID = $val; break;
			    case 'rq_user': $this->rq_user = $val; break;
			    case 'OPT': $this->OPT = $val; break;
			    case 'CMD': $this->CMD = $val; break;
			    default: die("<h2>ERRO: tentando State.set de propriedade inexistente, $name.</h2>");
			} // if switch

		return $val;
		// $_REQUEST[$name] e $_SERVER[$name] nao se altera?
	}

	function getPubProps()
	{	// for populate $this->allnames
		$pubProps = create_function('$obj', 'return get_object_vars($obj);');
		return $pubProps($this); // Returns only public properties,
		// an associative array of defined object accessible non-static properties
	}


	function States() { //por hora CONFIG=CONSTRUCTOR,  na mão. Popula estados!

		// zero ou minimo possivel de globals:
		global $DEBUG; 		// inicializar antes
		global $OPT;		// inicializada aqui
		global $MSG_ALERT; 	// inicializada aqui
		global $MYNAME;

		$this->OPT = $OPT       = strtolower(@$_REQUEST['opt']); // nao pode em session?
		$MSG_ALERT = '';
		$this->CMD	       = strtolower(@$_REQUEST['cmd']); // nao pode em session?

		// indica quais variaveis espera (de qq um ou apenas de get ou apenas session)
		// indica se prioridade é get ou sess

		$this->sessions = array('user','idp', 'S', 'cntID');
		$this->gets 	= array('cntID', 'OPT');
		$this->server 	= array('HTTP_HOST');
		$this->publics 	= array_keys($this->getPubProps()); // nomes publicos

		// ABAIXO INICIALIZACOES ESPECIFICAS DO SISFISIO!
		session_start();
		if ($DEBUG && $_SERVER['HTTP_HOST'] == 'localhost')
			ini_set('display_errors','On');


		if (@$_REQUEST['pg']) {
			$this->thispage= @$_REQUEST['pg'];
			$_SESSION['thispage'] = $this->thispage;
		} else
			$this->thispage = @$_SESSION['thispage'];
		if(!$this->thispage) {
			$_SESSION['thispage'] = $this->thispage = 'home';
		}
		if ($this->thispage=='sair') {
			//session_start();
			ADD_MSG_ALERT("SAINDO DO $MYNAME!");
			$_SESSION['user'] = '';
			$_SESSION['idp'] = '';
			$_SESSION['S'] = '';
			session_destroy();
			$this->thispage='home';
			$_SESSION['cntID']='0';
		}

		if (!@$_SESSION['S']) { // detecta se primeira vez no site
			ini_set('session.gc-maxlifetime', 1800); 
				// 60*30 number of SECONDS after which data will be seen as 'garbage' and potentially cleaned up
				//session_start(); // start unico seria aqui
			$_SESSION['cntID'] = '0';
			$_SESSION['S'] = 1;
		}

		if (strlen(@$_REQUEST['cntID'])) // pode ser zero
			$_SESSION['cntID']=$_REQUEST['cntID']; // força conforme navegação

		$this->thispageID = $this->thispage.$_SESSION['cntID'];
		$this->formID = "{$this->thispageID}-".($OPT? $OPT: 0); //opt global
		$this->rq_user = @$_REQUEST['user'];

		$this->LOGADO  = @$_SESSION['user'];
		$this->FORM_SUCESSO = 1;  // default muda com aviso de err.

	} // constructor


	function setLogin($rq_user, $id, $sis_tipo) {
		//global $LOGADO; // ??

		if ($sis_tipo) { // ou pdo erro!
			ADD_MSG_ALERT("Logando com $rq_user ... login ok.");
			$this->LOGADO = $_SESSION['user'] = $rq_user;
			$_SESSION['sis_tipo'] = $this->LOGADO_tipo = $this->sis_tipo  = $sis_tipo;  // REMOVER LOGADO_tipo
			$this->idp = $_SESSION['idp'] = $id;
			$this->FORM_SUCESSO=100; // ??
		} else
			ADD_MSG_ALERT("SENHA OU USER (<b>$rq_user</b>) INCORRETOS");
	}

}//class

?>
