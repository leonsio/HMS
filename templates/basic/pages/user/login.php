<?php 
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
?>
<div class="content-primary">
<h2>Login</h2>

<p>Bitte logen Sie sich ein:</p>

<form method="post" action="<?php echo BASE_URL; ?>user/login" >
<div data-role="fieldcontain">
<label for="username" class="ui-input-text">Benutzername:</label>
<input type="text" name="username" class="text" id="username" value="" />
</div>
<div data-role="fieldcontain">
<label for="password">Passwort:</label>
<input type="password" name="password" class="text" id="password" />
</div>
<div data-role="fieldcontain">
<input type="hidden" name="task" value="login" />
<input type="hidden" name="return_to" value="" />
<input type="submit" data-icon="check" class="button submit" value="Login">
</div>
</form>
</div>
<div class="content-secondary">
				<div data-theme="c" data-role="collapsible" data-collapsed="true"
					data-content-theme="c">
					
					<h3>Menu</h3>

					<ul data-role="listview" data-theme="c" data-dividertheme="d">
                           <li data-theme="c" ><a href="<?php echo BASE_URL; ?>user/login">Login</a></li>
                           <li data-theme="c" ><a href="<?php echo BASE_URL; ?>user/login">Über</a></li>
					</ul>
				</div>
</div>
