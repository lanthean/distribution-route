<?php /* Smarty version Smarty-3.0.7, created on 2011-07-21 14:27:41
         compiled from "/Data/WWW/ea/design/html/index.html" */ ?>
<?php /*%%SmartyHeaderCode:21448278904e281b3d3cd639-37873588%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c70847ec080c852a7f2a47f486de8f4e6d94a6d5' => 
    array (
      0 => '/Data/WWW/ea/design/html/index.html',
      1 => 1307315632,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21448278904e281b3d3cd639-37873588',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php echo $_smarty_tpl->getVariable('headcontent')->value;?>


</head>
    <body><?php echo $_smarty_tpl->getVariable('bodyScripts')->value;?>
<?php echo $_smarty_tpl->getVariable('debug')->value;?>

        <h1><?php echo $_smarty_tpl->getVariable('Title')->value;?>
</h1>
        <?php echo $_smarty_tpl->getVariable('body')->value;?>

        <div class="addons"><?php echo $_smarty_tpl->getVariable('addons')->value;?>
</div>
        
        <div id="Overlay" class="transparent">
            <h1>Data pro optimalizaci byla odeslána.
            <br />Počkejte prosím..
            </h1>
                <img src="<?php echo $_smarty_tpl->getVariable('config')->value['baseurl'];?>
design/img/loading/loading8.gif" /> <!-- 23, 8 -->
        </div>
    </body>
</html>