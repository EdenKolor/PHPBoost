<?php
/*##################################################
 *                           admin-modules-common.php
 *                            -------------------
 *   begin                : September 20, 2011
 *   copyright            : (C) 2011 Patrick DUBEAU
 *   email                : daaxwizeman@gmail.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

 ####################################################
 #                     French                       #
 ####################################################

$lang['module'] = 'Module';

//Title
$lang['modules.module_management'] = 'Gestion des modules';
$lang['modules.add_module'] = 'Ajouter un module';
$lang['modules.update_module'] = 'Mettre à jour un module';
$lang['modules.delete_module'] = 'Suppression ou désactivation d\'un module';
$lang['modules.delete_module_multiple'] = 'Suppression ou désactivation de modules';
$lang['modules.installed_modules'] = 'Modules installés';

//Warnings
$lang['modules.warning_before_install'] = '<span class="message-helper warning">Un module doit être activé, désactivé, installé ou supprimé uniquement depuis cette page. <br />En aucun cas il ne faut intervenir sur le FTP et/ou dans la base de données.</span>';

//Upload
$lang['modules.upload_module'] = 'Uploader un module';
$lang['modules.upload_description'] = 'L\'archive uploadée doit être au format zip ou gzip';

//Module
$lang['modules.name'] = 'Nom';
$lang['modules.description'] = 'Description';
$lang['modules.author'] = 'Auteur';
$lang['modules.compatibility'] = 'Compatibilité';
$lang['modules.php_version'] = 'Version PHP';
$lang['modules.url_rewrite_rules'] = 'Règles de réécriture d\'URL';
$lang['modules.page_admin'] = 'Administration';
$lang['modules.modules_available'] = 'Modules disponibles';
$lang['module.documentation'] = 'Documentation';

//Module management
$lang['modules.install_all_selected_modules'] = 'Installer les modules sélectionnés';
$lang['modules.uninstall_all_selected_modules'] = 'Désinstaller les modules sélectionnés';
$lang['modules.authorization'] = 'Autorisation d\'accès';

//Messages
$lang['modules.already_installed'] = 'Ce module est déjà installé';
$lang['modules.module_not_upgradable'] = 'Ce module ne peut pas être mis à jour';
$lang['modules.not_installed_module'] = 'Ce module n\'est pas installé !';
$lang['modules.updates_available'] = 'Mises à jour disponibles';
$lang['modules.config_conflict'] = 'Conflit avec la configuration du module, installation impossible!';
$lang['modules.not_compatible'] = 'Ce module n\'est pas compatible avec la version actuelle de PHPBoost vérifiez la disponibilité d\'une nouvelle version sur <a href="http://www.phpboost.com/download">le site de PHPBoost</a>.';

//Delete module
$lang['modules.drop_files'] = 'Supprimer tous les fichiers du module';
$lang['modules.drop_files_multiple'] = 'Supprimer tous les fichiers des modules';

//Update
$lang['modules.upgrade_module'] = 'Mettre à jour';
$lang['modules.upgrade_all_selected_modules'] = 'Mettre à jour les modules sélectionnés';
?>
