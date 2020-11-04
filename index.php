<?php
require_once 'init.php';
$theme  = 'theme3';
new TSession;

$content     = file_get_contents("app/templates/{$theme}/layout.html");
if( TSession::getValue('nivel') && TSession::getValue('logged') )
  $menu_string = AdiantiMenuBuilder::parse('menuadmin.xml', $theme);
else
  $menu_string = AdiantiMenuBuilder::parse('menu.xml', $theme);

$content     = str_replace('{MENU}', $menu_string, $content);
$content     = ApplicationTranslator::translateTemplate($content);
$content     = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$theme}/libraries.html"), $content);
$content     = str_replace('{class}', isset($_REQUEST['class']) ? $_REQUEST['class'] : '', $content);
$content     = str_replace('{template}', $theme, $content);
$content     = str_replace('{MENU}', $menu_string, $content);
$css         = TPage::getLoadedCSS();
$js          = TPage::getLoadedJS();
$content     = str_replace('{HEAD}', $css.$js, $content);

if (isset($_REQUEST['class']) && TSession::getValue('logged'))
{
    $user = TSession::getValue('user');
    $sair = "<a href='index.php?class=Logoff&method=logOff'>Sair</a>";

    $content  = str_replace('{opcao1}', $user , $content);
    $content  = str_replace('{opcao2}', $sair, $content);
    echo $content;
    $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
    AdiantiCoreApplication::loadPage($_REQUEST['class'], $method, $_REQUEST);
}
else{
    $content  = str_replace('{opcao1}', '' , $content);
    $content  = str_replace('{opcao2}', '', $content);
    echo $content;

   if ($_REQUEST['class'] == 'Validado' && !TSession::getValue('logged')) {
      $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
      AdiantiCoreApplication::loadPage($_REQUEST['class'], $method, $_REQUEST);
      //nao logado e com classe requisitada 
      
    }else {
      AdiantiCoreApplication::loadPage('Login', NULL, NULL);
    }
}  
