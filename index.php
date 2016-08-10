<?php
// Валидация
function validateNumber($value) {
    $error = '';
    $filter = FILTER_VALIDATE_INT;
    $options = array(
        'options' => array(
            'min_range' => 0    
        ),
    );
    if (empty($value) && $value!=="0") {
        $error = "Пожалауйста заполните поле.";    
    } elseif (!filter_var($value, $filter, $options)) {
        $error = "Значение должно быть целым числом, больше нуля."; 
    }
    return $error;    
}

// Копирование значений
function copyValue($value) {
    $value = trim($value);
    $value = stripslashes($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value);
    return $value;
}

// Скрипт расчета кредита
function calculateCredit($amount, $payment, $percent) {

    $creditBalance = $amount; 
    $monthlyPercent = $percent/100; 
    $monthlyPayment = $payment; 
    $paymentTotal = 0;  
    
    // Проверка на возможность выплаты кредита
    $monthlyGrowth = $amount * $monthlyPercent;
    if ($monthlyGrowth>=$payment) {        
        return "Выплатить кредит невозможно так как ежемесячный прирост <strong>{$monthlyGrowth}</strong> р.
        больше или равен ежемесячной выплаты";
    }
        
    // Расчет кредита
    for ($month = 1; $creditBalance > 0; $month ++) {
        $creditBalance += $creditBalance * $monthlyPercent;
    	
    	if ($creditBalance < $monthlyPayment) {            
    	    $monthlyPayment = $creditBalance;
        } 
        $creditBalance -= $monthlyPayment;
        $paymentTotal += $monthlyPayment;
          
            /* Если баланс отрицательный — хватит считать */
        if ($creditBalance <= 0) {
    	    break;
        }
    }
    // Результаты
    return "Время выплаты: <strong>{$month}</strong> месяцев, сумма выплаты: <strong>{$paymentTotal}</strong> р.";
}

// Проверяем была ли отправлена форма
$values = "";
$errors = array();
if (!empty($_GET)) {
    // Отправляем каждое поле формы на валидацию и копируем значение
    foreach($_GET as $key => $value) {        
        // Копируем значения
        $values[$key] = copyValue($value);
        // Сохраняем ошибки если они есть
        $error=validateNumber($value);
        if ($error){
            $errors[$key]=$error;
        } 
    }    
    // Выполняем скрипт расчета кредита если данные введены правильно
    if (empty($errors)) {
        $result = calculateCredit($values['amount'], $values['payment'], $values['percent']);
    }    
}
//Выводим форму($values, $errors);
require ('template.html');

?>
