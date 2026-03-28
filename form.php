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

.radio-group {
    display: flex;
    gap: 15px;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.checkbox-group input {
    width: auto;
}

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

<form id="myForm" method="POST">

<input name="name" placeholder="ФИО">
<input name="phone" placeholder="Телефон">
<input name="email" placeholder="Email">
<input type="date" name="birthdate">

<label>Пол:</label>
<div class="radio-group">
<label><input type="radio" name="gender" value="male"> М</label>
<label><input type="radio" name="gender" value="female"> Ж</label>
</div>

<select name="languages[]" multiple>
<option value="1">PHP</option>
<option value="2">Python</option>
<option value="3">Java</option>
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

    fetch("index.php", {
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

    setTimeout(() => {
        n.style.display = "none";
    }, 3000);
}
</script>

</body>
</html>
