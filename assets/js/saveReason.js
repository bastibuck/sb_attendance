/**
 * Javascript-Funktion für Anwesenheitsliste
 *
 * @copyright  Sebastian Buck 2014
 * @author     Sebastian Buck
 * @package    Attendance
 */

/*
 * Funktion zur Abfrage eines Abwesenheitsgrundes
 */
function saveReason(form_name)
{
    var reason = prompt("Grund?", "");

    if (reason != null && reason != '')
    {
        document.forms[form_name].reason.value = reason;
        return true;
    }
    else
    {
        return false;
    }
}