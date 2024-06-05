<?php
    class Model {
        //arrays
        protected $input;
        protected $output;
        protected $operators;
        protected $num_holder;
        protected $converstack;
        protected $evalstack;
        protected $postfix;
        //precedences
        protected $stack_prec;
        protected $elem_prec;
        //counters
        protected $open_prnth_ctr;
        protected $close_prnth_ctr;
        //switches
        protected $operator_b4;
        protected $decipoint_b4;
        protected $open_p_b4;
        protected $close_p_b4;
        //others
        protected $output_var;
        
        public function __construct() {
            $this->input = array();
            $this->output = array();
            $this->operators = array("^", "*", "/", "+", "-", "~");
            $this->num_holder = array();
            $this->converstack = array("(");
            $this->evalstack = array();
            $this->postfix = array();
            $this->stack_prec = 0;
            $this->elem_prec = 0;
            $this->open_prnth_ctr = 1; //because the first is in the stack
            $this->close_prnth_ctr = 0; //+1 because of the last element appended in $input
            $this->operator_b4 = false;
            $this->decipoint_b4 = false;
            $this->open_p_b4 = false;
            $this->close_p_b4 = false;
            $this->output_var = 0;
        }
        
        //checks precedence of current element
        public function check_prec($item) {
            switch ($item) {
                case $this->operators[0]:
                    return 3;
                    break;
                case $this->operators[1]:
                    return 2;
                    break;
                case $this->operators[2]:
                    return 2;
                    break;
                case $this->operators[3]:
                    return 1;
                    break;
                case $this->operators[4]:
                    return 1;
                    break;
                default:
                    return 0;
            }
        }
        
        //checks if current element is an operator
        public function in_operators($item) {
            return in_array($item, $this->operators);
        }
        
        //pushes number to $num_holder
        public function push_to_holder($item) {
            array_push($this->num_holder, $item);
        }
        
        //pushes number to $output
        public function out_num() {
            $temp = implode("", $this->num_holder);
            array_push($this->output, $temp);
            $this->num_holder = array();
        }
        
        //pushes operator from $converstack to $output and pops $converstack
        public function out_operator() {
            array_push($this->output, end($this->converstack));
            array_pop($this->converstack);
        }
        
        //pushes operator to $converstack
        public function push_operator($item) {
            array_push($this->converstack, $item);
        }
        
        //pushes all operators to $output after equals() loop is finished
        public function unload() {
            foreach (array_reverse($this->converstack) as $unload) {
                if ($unload == "(") {
                    unset($this->converstack[array_search($unload, $this->converstack)]);
                    break;
                }
                else {
                    array_push($this->output, $unload);
                    array_pop($this->converstack);
                }
            }
        }
        
        public function convert() {
            foreach ($this->output as $elemview) {
                if ($elemview == "~") $elemview = "-";
                array_push($this->postfix, $elemview);
            }
            
            $this->postfix = implode("", $this->postfix);
        }
        
        public function error() {
            $this->output = "ERROR";
        }
    }
    
    class Controller extends Model {
        protected $model;
        public function __construct($model) {
            $this->model = $model;
        }
        
        public function equals() {
            /* CONVERSION */
            //removes whitespaces and splits input into an array
            $this->model->input = str_split(preg_replace("/\s+/", "", $_GET["input"]));
            
            array_push($this->model->input, ")"); //refer to line 27
            
            foreach($this->model->input as $elem) {
                //checks if $elem is a number
                if (is_numeric($elem)) {
                    $this->model->push_to_holder($elem);
                    $this->model->operator_b4 = false;
                    $this->model->open_p_b4 = false;
                    $this->model->close_p_b4 = false;
                }
                
                //checks if $elem is a decimal point
                else if ($elem == "." and $this->model->decipoint_b4 == false) {
                    $this->model->push_to_holder($elem);
                    $this->model->decipoint_b4 = true;
                }
                
                //checks if $elem is an operator; throws to error block if end of $num_holder is a decimal point
                else if ($this->model->in_operators($elem) and end($this->model->num_holder) != "." ) {
                    if(!empty($this->model->num_holder))
                       {
                           $this->model->out_num();
                           
                           //resets $num_holder
                           $this->model->num_holder = array();
                       }
                    
                    //checks negative sign
                    if ($this->model->open_p_b4 == true) {
                        if ($elem == "-") $elem = "~";
                        else {
                            $this->model->error();
                            break;
                        }
                    }
                    //if not negative
                    else {
                        //check the precedence of current element
                        $this->model->elem_prec = $this->model->check_prec($elem);

                        //check the precedence of the element on top of stack
                        $this->model->stack_prec = $this->model->check_prec(end($this->model->converstack));

                        //compare precedences
                        if ($this->model->stack_prec >= $this->model->elem_prec) {
                            $this->model->out_operator();
                        }
                    }
                    
                    $this->model->push_operator($elem);
                    $this->model->operator_b4 = true;
                    $this->model->decipoint_b4 = false;
                    $this->model->open_p_b4 = false;
                }
                
                //checks if $elem is a close parenthesis
                else if ($elem == ")" and $this->model->operator_b4 == false) {
                    if(!empty($this->model->num_holder))
                    {
                        $this->model->out_num();
                    }
                    $this->model->unload();
                    $this->model->close_prnth_ctr += 1;
                    $this->model->close_p_b4 = true;
                }
                
                //checks if $elem is an open parenthesis
                else if ($elem == "(" and $this->model->close_p_b4 == false) {
                    array_push($this->model->converstack, $elem);
                    $this->model->open_prnth_ctr += 1;
                    $this->model->open_p_b4 = true;
                }
                
                //general error trapping
                else {
                    $this->model->error();
                    break;
                }
                
                /* Output loop for developer reference */
                /*print_r($this->model->input);
                echo "<br>";
                echo "<b>Element: </b>" . $elem;
                echo "<br>" . "<b>Output: </b>";
                print_r($this->model->output);
                echo "<br>" . "<b>Stack: </b>";
                print_r($this->model->converstack);
                echo "<br>" . "<b>Num Holder: </b>";
                print_r($this->model->num_holder);
                echo "<br>" . "<b>Elem Prec: </b>" . $this->model->elem_prec;
                echo "<br>" . "<b>Stack Prec: </b>" . $this->model->stack_prec;
                echo "<br>" . "<b>Open Parenth: </b>" . $this->model->open_prnth_ctr;
                echo "<br>" . "<b>Close Parenth: </b>" . $this->model->close_prnth_ctr;
                echo "<br><br>";*/
            }
            //unloads non-empty num holder
            if (!empty($this->model->num_holder)) {
                $this->model->out_num();
            }
            
            //unloads non-empty stack
            if (!empty($this->model->converstack)) 
            {
                $this->model->unload();
            } 
            
            if ($this->model->open_prnth_ctr != $this->model->close_prnth_ctr) {
                $this->model->error();
            }
            
            $this->model->convert();
            
            echo '<h4>Postfix Notation: '. $this->model->postfix . '</h4>';
            
            foreach($this->model->output as $eval) {
                if (is_numeric($eval))
                {
                    array_push($this->model->evalstack, $eval);
                    echo '<br><span style="color:white;">'; 
                    print_r($this->model->evalstack); 
                    echo '</span>';
                }

                else
                {
                    $num2 = array_pop($this->model->evalstack);

                    if ($eval == '~')
                    {
                        $this->model->output_var = (0 - $num2);
                        array_push($this->model->evalstack, $this->model->output_var);
                        echo '<br><span style="color:white;">'; print_r($this->model->evalstack); 
                        echo '</span>';
                    }
                    else
                    {
                        $num1 = array_pop($this->model->evalstack);

                        echo '<br><span style="color:white;">';
                        print_r($this->model->evalstack); echo '</span>';

                        switch($eval)
                        {
                            case '^': 
                                $this->model->output_var = pow($num1,$num2);
                                break;

                            case '*': 
                                $this->model->output_var = ($num1 * $num2);
                                break;

                            case '/': 
                                $this->model->output_var = ($num1 / $num2);
                                break;

                            case '+': 
                                $this->model->output_var = ($num1 + $num2); 
                                break;

                            case '-': 
                                $this->model->output_var = ($num1 - $num2);
                                break;
                        }
                        echo '<span style="color:goldenrod;">' . $num1 . " " . $eval . " " . $num2 . " = " . $this->model->output_var . '</span>';

                        array_push($this->model->evalstack, $this->model->output_var);
                        echo '<br><span style="color:white;">'; print_r($this->model->evalstack);
                        echo '</span>';
                    }
                }
            }
            
            echo "<br><br><h2>Final Answer: " . array_pop($this->model->evalstack) . "</h2>";
        }
    }
    
    class View extends Controller {
        protected $controller;
        protected $model;
        public function __construct($controller, $model) {
            $this->controller = $controller;
            $this->model = $model;
        }
        
        public function output_postfix() {
            return $this->model->postfix;
        }
        
        public function output_eval() {
            return $this->model->output_var;
        }
    }
?>