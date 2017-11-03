<?
require_once "backup/lib/Dropbox/autoload.php"; //подгружаем библеотеку для работы с Dropbox

use \Dropbox as dbx;

//Ссылка для получения нового: https://www.dropbox.com/developers/apps/info/ Там будет кнопка "Generated access token";
$token  	= ''; 
$dbhost     = ''; // Адрес сервера MySQL, обычно localhost
$dbuser     = '';   // имя пользователя базы данных
$dbpass     = ''; // пароль пользователя базы данных
$dbname     = "";  // название базы данных
$dropboxDir = 'backup'; // папочка DropBox в которой будете хранить бэкапы 

// все что ниже лучше не трогать -- тут происходит магия. :)

$dbbackup = $dbname .'_'. date("Y-m-d_H-i-s") . '.sql.gz';
$filebackup = 'file_backup_'. date("Y-m-d_H-i-s") .'.tar.gz';
$backupdir = dirname(__FILE__);

system("mysqldump -h $dbhost -u $dbuser --password='$dbpass' $dbname | gzip > $dbbackup");
exec("tar cfzv $filebackup $backupdir");

$dbxClient = new dbx\Client($token, "PHP-Example/1.0");

uploadBackupDB($dbbackup, $dropboxDir, $dbxClient);
uploadMainDir($filebackup, $dropboxDir, $dbxClient);

	unlink($dbbackup);
	unlink($filebackup); 

function uploadBackupDB($dbbackup, $dropboxDir, $dbxClient)
{
	$fd = fopen($dbbackup, "rb");
	$md = $dbxClient->uploadFile('/'.$dropboxDir.'/'.$dbbackup.$name,
                           dbx\WriteMode::add(), $fd);
	fclose($fd);
 if(isset($md['id'])){
 print 'База данных успешно скопирована, ID: '.$md['id'].'</br>';
}
}

function uploadMainDir($filebackup, $dropboxDir, $dbxClient)
{
	$fd = fopen($filebackup, "rb");
	$md = $dbxClient->uploadFile('/'.$dropboxDir.'/'.$filebackup.$name,
                           dbx\WriteMode::add(), $fd);
	fclose($fd);
 if(isset($md['id'])){
 print 'Корневой каталог успешно скопирован, ID: '.$md['id'];

}
}
