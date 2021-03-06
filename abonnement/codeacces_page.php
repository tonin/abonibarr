<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *   	\file       dev/Codeaccess/Codeacces_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2015-06-24 01:20
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
include_once('class/codeacces.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/contract.lib.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");

// Get parameters
//var_dump($_REQUEST);exit;
$id			=  (GETPOST('orderid')?GETPOST('orderid'):GETPOST('id','int'));
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='create';

// Load object if id or ref is provided as parameter
$object=new Codeacces($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

// Action to add record
if ($action == 'add')
{
	if (GETPOST('cancel'))
	{
		$urltogo=$backtopage?$backtopage:dol_buildpath('/abonnement/codeacces_page.php?id='.$id.'&action=list',1);
		header("Location: ".$urltogo);
		exit;
	}

	$error=0;

	/* object_prop_getpost_prop */
	$object->prop1=GETPOST("field1");
	$object->prop2=GETPOST("field2");

	if (empty($object->ref))
	{
		$error++;
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),'errors');
	}

	if (! $error)
	{
		$result=$object->create($user);
		if ($result > 0)
		{
			// Creation OK
			$urltogo=$backtopage?$backtopage:dol_buildpath('/mymodule/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}
		{
			// Creation KO
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else  setEventMessages($object->error, null, 'errors');
			$action='create';
		}
	}
	else
	{
		$action='create';
	}
}

// Cancel
if ($action == 'update' && GETPOST('cancel')) $action='view';

// Action to update record
if ($action == 'update' && ! GETPOST('cancel'))
{
	$error=0;

	$object->prop1=GETPOST("field1");
	$object->prop2=GETPOST("field2");

	if (empty($object->ref))
	{
		$error++;
		setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")),null,'errors');
	}

	if (! $error)
	{
		$result=$object->update($user);
		if ($result > 0)
		{
			$action='view';
		}
		else
		{
			// Creation KO
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
			$action='edit';
		}
	}
	else
	{
		$action='edit';
	}
}

// Action to delete
if ($action == 'confirm_delete')
{
	$result=$object->delete($user);
	if ($result > 0)
	{
		// Delete OK
		setEventMessages($langs->trans("RecordDeleted"), null, 'mesgs');
		header("Location: ".dol_buildpath('/buildingmanagement/list.php',1));
		exit;
	}
	else
	{
		if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
		else setEventMessages($object->error,null,'errors');
	}
}





/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','MyPageName','');

$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_needroot();
	});
});
</script>';
$ref=GETPOST('ref','alpha');
$contrat = new Contrat($db) ;
$result = $contrat->fetch($id, $ref) ;
$head = contract_prepare_head($contrat);
$hselected=2;

dol_fiche_head($head, $hselected, $langs->trans("Contract"), 0, 'contract');
//var_dump($head);exit;
if ($object->fetch($id, $ref) > 0)
{

}
// Part to show a list
if ($action == 'list' || empty($id))
{
    $sql = "SELECT";
    $sql.= " t.rowid,";
    
		$sql.= " t.entity,";
		$sql.= " t.ref_ext,";
		$sql.= " t.lastname,";
		$sql.= " t.firstname,";
		$sql.= " t.login,";
		$sql.= " t.pass,";
		$sql.= " t.fk_soc,";
		$sql.= " t.note,";
		$sql.= " t.datevalid,";
		$sql.= " t.datec,";
		$sql.= " t.tms,";
		$sql.= " t.fk_user_author,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.fk_user_valid,";
		$sql.= " t.canvas,";
		$sql.= " t.import_key";

    
    $sql.= " FROM ".MAIN_DB_PREFIX."code_acces as t";
    $sql.= " WHERE t.fk_soc = '$user->societe_id'";
    $sql.= " ORDER BY t.rowid ASC";

    print '<table class="noborder">'."\n";
    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans('login'),$_SERVER['PHP_SELF'],'t.login','','','',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('pass'),$_SERVER['PHP_SELF'],'','',$param,'',$sortfield,$sortorder);
    print '</tr>';

    dol_syslog($script_file, LOG_DEBUG);
    $resql=$db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        while ($i < $num)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                // You can use here results
                print '<tr><td>';
                print $obj->field1;
                print $obj->field2;
                print '</td></tr>';
            }
            $i++;
        }
    }
    else
    {
        $error++;
        dol_print_error($db);
    }

    print '</table>'."\n";
}



// Part to create
if ($action == 'create')
{
	print_fiche_titre($langs->trans("NewResidence"));

	dol_fiche_head();

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	print '<table class="border centpercent">'."\n";
	print '<tr><td class="fieldrequired">'.$langs->trans("Login").'</td><td>';
	print '<input class="flat" type="text" size="36" name="login" value="'.$label.'">';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans("Mot de passe").'</td><td>';
	print '<input class="flat" type="text" size="36" name="motdepasse" value="'.$label.'">';
	print '</td></tr>';

	print '</table>'."\n";

	print '<br>';

	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></center>';

	print '</form>';

	dol_fiche_end();
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	dol_fiche_head();

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';


	print '<br>';

	print '<center><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"></center>';

	print '</form>';

	dol_fiche_end();
}



// Part to show record
if ($id && (empty($action) || $action == 'view'))
{
	dol_fiche_head();



	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->mymodule->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->mymodule->delete)
		{
			if ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile))	// We can't use preloaded confirm form with jmobile
			{
				print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
			}
			else
			{
				print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
			}
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	// The class must extends CommonObject class to have this method available
	//$somethingshown=$object->showLinkedObjectBlock();

}


// End of page
llxFooter();
$db->close();
