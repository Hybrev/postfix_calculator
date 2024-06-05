<?php
    require_once("classes.php");
    $model = new Model();
    $controller = new Controller($model);
    $view = new View($controller, $model);
?>

<html>
    <head>
        <title>Postfix and Stack Calculator</title>
        
        <!-- Meta -->
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Links -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link rel="stylesheet" href="style.css">
        
        <!-- Script -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="eval-container">
            <div class="eval-title"><h1>Postfix Evaluation (Stack)</h1></div>
            <div class="evaluation">
                <?php if (isset($_GET['input'])): $controller->equals(); endif;?>
            </div>
        </div>
        <div class="calc-container">
            <div class="calculator">
                <form class="calc-form" method="get" name="calc">
                    <div class="calc-section" id="screen">
                        <div class="panel" id="output">
                            <input class="panel-input" type="text" name="postfix" id="result" value="<?php if (empty($_GET['input'])) echo "postfix notation goes here..."; else echo $view->output_postfix(); ?>" readonly>
                        </div>
                        
                        <div class="panel" id="output">
                            <input class='panel-input' type="text" name="eval" id="result" value="<?php if (empty($_GET['input'])) echo "postfix evaluation goes here..."; else echo $view->output_eval();?>" readonly>
                        </div>
                        
                        <div class="panel" id="input">
                            <input class="panel-input" type="text" name="input" id="display" readonly>
                        </div>
                    </div>
                    <div class="calc-section" id="keys">
                        <table align="center">
                            <tr>
                                <td class="key-cell"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="(" onclick="calc.display.value += '('"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value=")" onclick="calc.display.value += ')'"></td>
                                <td class="key-cell"><input type="reset" class="key" id="ac"
                               value="AC"></td>
                            </tr>
                            <tr>
                                <td class="key-cell"><input type="button" class="key" 
                               value="7" onclick="calc.display.value += '7'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="8" onclick="calc.display.value += '8'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="9" onclick="calc.display.value += '9'"></td>
                                <td class="key-cell"><input type="button" class="key" onclick="calc.display.value += '/'"
                               value="/"></td>
                            </tr>
                            <tr>
                                <td class="key-cell"><input type="button" class="key" 
                               value="4" onclick="calc.display.value += '4'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="5" onclick="calc.display.value += '5'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="6" onclick="calc.display.value += '6'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="*" onclick="calc.display.value += '*'"></td>
                            </tr>
                            <tr>
                                <td class="key-cell"><input type="button" class="key" 
                               value="1" onclick="calc.display.value += '1'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="2" onclick="calc.display.value += '2'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="3" onclick="calc.display.value += '3'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="-" onclick="calc.display.value += '-'"></td>
                            </tr>
                            <tr>
                                <td class="key-cell"><input type="button" class="key" 
                               value="." onclick="calc.display.value += '.'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="0" onclick="calc.display.value += '0'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="^" onclick="calc.display.value += '^'"></td>
                                <td class="key-cell"><input type="button" class="key" 
                               value="+" onclick="calc.display.value += '+'"></td>
                            </tr>
                            <tr>
                                <td class="key-cell"></td>
                                <td class="key-cell"></td>
                                <td class="key-cell"></td>
                                <td class="key-cell"><input type="submit" class="key" id="equals" 
                               value="="></td>
                            </tr>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>