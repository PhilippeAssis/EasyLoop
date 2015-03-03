<?php

/*
	********************************************************
	Easy Loop
	Criado por: Philippe Assis
	Website: www.philippeassis.com
	Documentação: blog.philippeassis.com/php/easyloop
	********************************************************
	Melhor uso da classe, usando a classe Easy Mysql ( blog.philippeassis.com/php/easymysql )
	
	EXEMPLO DE USO COM O EASY MYSQL
	
	//Ultilizando o Easy Mysql para pegar os dados
		$mysql = new easymysql(...);
		//...
		$result = $mysql->select('products','name');
		
	$loop = new easyloop($result); 
	// Ou 
	// $loop = new easyloop(); 
	// $loop->result($result);
	
	if($loop->exists()) : while($loop->init()):
		$loop->e('id','<h2>','</h2>') // Imprime: <h2>Notebook</h2>...
		$loop->echo_('id','<h2>','</h2>') // Imprime: <h2>Notebook</h2>...
		$loop->r('id','<h2>','</h2>') // Retorna: <h2>Notebook</h2>...
		$loop->return_('id','<h2>','</h2>') // Retorna: <h2>Notebook</h2>...
	endwhie; else:
		echo "<h2>Nenhum produto encontrado</h2>";
	endif;
	
	
	********************************************************
	Para mais instruções de uso, acesse blog.philippeassis.com/php/easyloop
		
*/



class EasyLoop
{
    public $result = null; // Resultado da pesquisa
    public $current = null; // Resultado da pesquisa atual
    public $matrix = null; // Resultado da pesquisa
    public $count = 0;// quantidade de linhas
    public $defaultUnicKey = 'result'; // Key do valor do loop, caso o resultado recebido seja unico (uma string)
    public $start = false; //Define que o loop já começou
    public $prepend = null; // Conteudo anterior ao resultado
    public $append = null;// Conteudo posterior ao resultado
    public $countLoop = 0;
    public $replace = null;
    public $slashes = true;// Remover barra invertida "\"


    /* *************************************************
    Captura e trata os dados recebidos para o loop
    ************************************************* */
    function result($result)
    {
        $this->result = $this->objectToArray($result);
        $this->count = count($result);
    }

    /* *************************************************
        INICIA O LOOP
        Exemplo de uso:
            while(init()){}
    ************************************************* */
    function init()
    {
        ++$this->countLoop;// Declara posição do loop

        if (!$this->start) {
            $this->clear();
            $this->start = true;
            $this->countLoop = 1;
        }

        if (!$this->result or is_null($this->result))
            return false;

        if (is_null($this->current))
            $this->current = current($this->result);
        else
            $this->current = next($this->result);

        if (!$this->current) {
            $this->start = false;
            $this->clear();
            return false;
        } else
            return true;

    }

    /* *************************************************
    Verifica a existencia de valores para o loop
    ************************************************* */
    function exists()
    {// Verifica a existencia de resultados
        if (!$this->result or is_null($this->result))
            return false;
        return true;
    }

    /* *************************************************
    Captura e trata os dados recebidos para o loop enviando os para result().
    IMPORTANTE: Isso não é obrigatorio na construção da classe
    ************************************************* */
    function __construct($result = false)
    {
        if ($result)
            $this->result($result);
    }

    /* *************************************************
        METODOS DE RESULTADO
    ************************************************* */
    // Retorna um resultado
    function post($key, $tags = true, $append = '')
    {
        $this->replace($key);

        if (!isset($this->current[$key]))
            return null;


        if ($tags === true and !is_null($this->prepend))
            return $this->prepend . $this->slashes($this->current[$key]) . $this->append;
        elseif (isset($tags) and $tags !== true)
            return $tags . $this->slashes($this->current[$key]) . $append;
        else
            return $this->slashes($this->current[$key]);
    }

    // Escreve o resultado
    function echo_($key, $tags = true, $append = '')
    {
        echo $this->post($key, $tags, $append);
    }

    // O mesmo que echo_
    function e($key, $tags = true, $append = '')
    {
        echo $this->post($key, $tags, $append);
    }

    // Retorna o resultado
    function return_($key, $tags = true, $append = '')
    {
        return $this->post($key, $tags, $append);
    }

    // O mesmo que return_
    function r($key, $tags = true, $append = '')
    {
        return $this->post($key, $tags, $append);
    }

    // Printa toda a linha com print_r
    function printCurrent()
    {
        print_r($this->current);
    }

    function jsonDecode($key)
    {
        return json_decode($this->post($key, false));
    }


    /* *************************************************
        FERRAMENTAS DA CLASSE
    ************************************************* */
    //Substitue keys
    function replace(&$key)
    {
        $key = isset($this->replace[$key]) ? $this->replace[$key] : $key;
    }

    // Retorna a quantidade de linhas
    function count($echo = true)
    {
        if ($echo)
            echo $this->count;
        else
            return $this->count;
    }

    // Retorna a de impressões por loop
    function countLoop($echo = true)
    {
        if ($echo)
            echo $this->countLoop;
        else
            return $this->countLoop;
    }

    //Transforma objeto em array
    function objectToArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = [];
            foreach ($data as $key => $value)
                $result[$key] = $this->objectToArray($value);

            return $result;
        }
        return $data;
    }

    // Remover barra invertida
    function slashes($string)
    {
        return ($this->slashes) ? stripslashes($string) : $string;
    }

    //Limpa os principais valores cacheados da classe
    function clear()
    {
        if ($this->result)
            reset($this->result);
        $this->current = null;
        $this->countLoop = 0;
    }
}