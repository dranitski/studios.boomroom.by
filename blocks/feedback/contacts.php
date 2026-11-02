<?
if (array_key_exists('messageFF', $_POST)) {
  mail ("iluffka999@gmail.com",
        "заполнена контактная форма с ".$_SERVER['HTTP_REFERER'],
        "Имя: ".$_POST['nameFF']."\nEmail: ".$_POST['contactFF']."\nТема: ".$_POST['temaFF']."\nСообщение: ".$_POST['messageFF']);
  echo $_POST['nameFF'];
}
?>