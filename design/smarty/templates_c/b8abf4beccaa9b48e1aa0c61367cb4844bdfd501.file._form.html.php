<?php /* Smarty version Smarty-3.0.7, created on 2011-07-21 14:27:40
         compiled from "/Data/WWW/ea/design/html/_form.html" */ ?>
<?php /*%%SmartyHeaderCode:10372932824e281b3ce00886-28602267%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b8abf4beccaa9b48e1aa0c61367cb4844bdfd501' => 
    array (
      0 => '/Data/WWW/ea/design/html/_form.html',
      1 => 1307434580,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10372932824e281b3ce00886-28602267',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="center" style="margin-top: 25px;">
    <h1>Optimalizace obchodní trasy</h1>
    <?php if (isset($_smarty_tpl->getVariable('addons',null,true,false)->value)){?><div class="addons"><?php echo $_smarty_tpl->getVariable('addons')->value;?>
</div><?php }?>
    <form action="" method="post">
<!--        <?php echo $_smarty_tpl->getVariable('config')->value['baseurl'];?>
<?php echo $_smarty_tpl->getVariable('script')->value;?>
/-->
<table>
    <tbody>
        <tr>
            <td><!-- table addresses -->
            <table>
                <tbody>
                    <tr><td colspan="2" class="center">
                        <h3>Zadejte jednotlivé body překládky</h3>
                        <p>Adresy vkládejte ve tvaru např: Gorkého 20, 60200 Brno,
                            <br />nebo ve formátu GPS souřadnic např: 49.20853N, 16.55968E</p>
<!--                        49.20853N, 16.55968E 49°11'59.284"N, 16°35'50.115"E-->
                        </td>
                    </tr>
                    
                    <?php  $_smarty_tpl->tpl_vars['text'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('Rows')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['text']->key => $_smarty_tpl->tpl_vars['text']->value){
 $_smarty_tpl->tpl_vars['name']->value = $_smarty_tpl->tpl_vars['text']->key;
?>
                    <tr><td class="left_lb"><?php echo $_smarty_tpl->tpl_vars['text']->value;?>
 adresa:</td>
                        <td><input id="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" type="text" name="address[<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
]" value="<?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->tpl_vars['name']->value];?>
" />
                            <?php if (isset($_smarty_tpl->getVariable('error_msg',null,true,false)->value[$_smarty_tpl->tpl_vars['name']->value])){?><br /><span class="error"><?php echo $_smarty_tpl->getVariable('error_msg')->value[$_smarty_tpl->tpl_vars['name']->value];?>
</span><?php }?></td></tr>
                    <?php }} ?>
                    <tr><td colspan="2" class="right">
                            <a href="<?php echo $_smarty_tpl->getVariable('sampleLocHref')->value;?>
" target="_blank">přenastavené lokace mapy.cz</a>
                        </td></tr>
                </tbody>
            </table>        
                
            </td>
            <td><!-- table settings -->
                <table>
                    <tbody>
                    <tr><td colspan="2" class="center">
                            <h3>Nastavení Evolučního algoritmu</h3>
                            <p>&nbsp;</br>&nbsp;</p>
                        </td></tr>
                    <tr><td>Nastavení typu fitness:</td>
                        <td><select id="TravelMethod" class="short" name="TravelMethod">
                                <option value="distance" selected="true">Vzdálenost</option>
                                <option value="duration">Čas</option>
                            </select>
<!--                            <input class="radiobutton" id="radio1" type="radio" name="TravelMethod" value="distance" checked="true" />
                            <label for="radio1">Vzdálenost</label>
                            &nbsp;&nbsp;&nbsp;
                            <input class="radiobutton" id="radio2" type="radio" name="TravelMethod" value="duration" />
                            <label for="radio2">Čas</label>-->
                        </td>
                    </tr>
                    <tr><td>Velikost populace:</td>                        
                        <td><select id="PopulationSize" class="short" name="PopulationSize">
                                <option name="5">5</option>
                                <option name="10" selected="true">10</option>
                                <option name="15">15</option>
                            </select>
                        </td>
                    </tr>
    <!--                        <input id="PopulationSize" class="short" type="text" name="PopulationSize" value="10" />
                            <?php if (isset($_smarty_tpl->getVariable('PopulationSize_err',null,true,false)->value)){?><br /><span class="error"><?php echo $_smarty_tpl->getVariable('PopulationSize_err')->value;?>
</span><?php }?></td></tr>-->
                    <tr><td>Počet populací (ukončující podmínka):</td>
                        <td><select id="MaxPopulationNumber" class="short" name="MaxPopulationNumber">
                                <option name="50">50</option>
                                <option name="100" selected="true">100</option>
                                <option name="150">150</option>
                            </select>
                        </td>
                    </tr>
                    <tr><td>Maximální stagnace fitness populace (pomocná u.p.):</td>
                        <td><select id="MaxDecayOfFitness" class="short" name="MaxDecayOfFitness">
                                <option name="50">50</option>
                                <option name="100" selected="true">100</option>
                                <option name="150">150</option>
                            </select>
                        </td>
                    </tr>
<!--                    <tr><td colspan="2">&nbsp;<a href="#Overlay" onclick="StartIdle()">overlay</a> </td></tr>-->
                    </tbody>
                </table>
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td colspan="2" class="right"><input type="submit" name="_form" value="Odeslat" onclick="StartIdle()"/></td>
        </tr>
    </tbody>
</table>
    </form>
</div>