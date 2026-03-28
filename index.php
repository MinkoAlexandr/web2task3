<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pdo = new PDO('mysql:host=localhost;dbname=mydb;charset=utf8', 'webuser', '12345');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 👉 Если POST — обрабатываем
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
if (!preg_match('/^[0-9+()\- ]+$/', $phone)) {
    echo json_encode([
        "status"=>"error",
        "message"=>"Телефон содержит недопустимые символы"
    ]);
    exit;
}
    $email = $_POST['email'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $languages = $_POST['languages'] ?? [];
    $contract = isset($_POST['contract']) ? 1 : 0;

    if (!$name) {
        echo json_encode(["status"=>"error","message"=>"Введите ФИО"]); exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status"=>"error","message"=>"Ошибка email"]); exit;
    }

    if (!$languages) {
        echo json_encode(["status"=>"error","message"=>"Выберите язык"]); exit;
    }

    if (!$contract) {
        echo json_encode(["status"=>"error","message"=>"Подтвердите контракт"]); exit;
    }

    // 💾 сохраняем ВСЕ поля
    $stmt = $pdo->prepare("
        INSERT INTO applications (name, phone, email, birthdate, gender, bio, contract)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $phone, $email, $birthdate, $gender, $bio, $contract]);

    $id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");

    foreach ($languages as $lang) {
        $stmt->execute([$id, $lang]);
    }

    echo json_encode(["status"=>"success","message"=>"Сохранено"]);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lab3</title>

<style>
body {
    font-family: Arial;
    background: linear-gradient(135deg, #74ebd5, #9face6);
    padding: 40px;
}

.container {
    background: white;
    padding: 30px;
    max-width: 500px;
    margin: auto;
    border-radius: 10px;
}

input, textarea, select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
}

button {
    background: green;
    color: white;
    padding: 10px;
    width: 100%;
}

/* ✅ фикс радио */
.radio-group {
    display: flex;
    gap: 20px;
    margin-top: 10px;
}

.radio-group label {
    font-weight: normal;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* ✅ фикс чекбокс */
.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.checkbox-group input {
    width: auto;
}

/* уведомления */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: green;
    color: white;
    padding: 10px;
    display: none;
}

.error {
    background: red;
}
</style>

</head>
<body>

<div class="container">
<h2>Форма</h2>

<form id="myForm">

<input name="name" placeholder="ФИО">
<input name="phone" placeholder="Телефон" pattern="[0-9+()\- ]+" required>
<input name="email" placeholder="Email">
<input type="date" name="birthdate">

<label>Пол:</label>
<div class="radio-group">
<label><input type="radio" name="gender" value="male"> М</label>
<label><input type="radio" name="gender" value="female"> Ж</label>
</div>

<br>

<!-- ✅ ВСЕ языки -->
<select name="languages[]" multiple>
<option value="1">Pascal</option>
<option value="2">C</option>
<option value="3">C++</option>
<option value="4">JavaScript</option>
<option value="5">PHP</option>
<option value="6">Python</option>
<option value="7">Java</option>
<option value="8">Haskell</option>
<option value="9">Clojure</option>
<option value="10">Prolog</option>
<option value="11">Scala</option>
<option value="12">Go</option>
</select>

<textarea name="bio" placeholder="Биография"></textarea>

<div class="checkbox-group">
<span>С контрактом ознакомлен</span>
<input type="checkbox" name="contract">
</div>

<button type="submit">Отправить</button>

</form>
</div>

<div id="notify" class="notification"></div>

<script>
document.getElementById("myForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("", { // 👉 отправка в этот же файл
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        showNotification(data.message, data.status === "error");
    })
    .catch(() => {
        showNotification("Ошибка соединения", true);
    });
});

function showNotification(text, isError) {
    let n = document.getElementById("notify");
    n.innerText = text;
    n.style.display = "block";

    if (isError) {
        n.classList.add("error");
    } else {
        n.classList.remove("error");
    }

    setTimeout(() => n.style.display = "none", 3000);
}
</script>

</body>
</html>
