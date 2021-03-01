<?php
class mail_storage_monitoring extends rcube_plugin
{
  function init()
  {
    $this->load_config();
    $this->add_texts("localization/", true);
    $this->include_script("mail_storage_monitoring.js");

    $this->add_hook('render_page', array($this, 'check_mail_storage'));
    $this->add_hook('refresh', array($this, 'check_mail_storage'));
  }

  /**
   * Checks if a given filesystem path is a mountpoint.
   */
  function is_mountpoint($path)
  {
    $mountpoint_stat = stat($path);
    if (!$mountpoint_stat || !($mountpoint_stat["mode"] & 0040000))
      return NULL;

    $parent_stat = stat(dirname($path));
    if (!$mountpoint_stat || !($parent_stat["mode"] & 0040000))
      return NULL;

    return $mountpoint_stat["ino"] != $parent_stat["ino"] && $mountpoint_stat["dev"] != $parent_stat["dev"];
  }

  /**
   * Displays an error and blocks the UI if the system's mail storage is not mounted.
   */
  function check_mail_storage()
  {
    $rcmail = rcmail::get_instance();

    $mail_storage_online = $this->is_mountpoint($rcmail->config->get('mail_storage_monitoring_mountpoint'));

    if ($rcmail->action != "refresh" && $rcmail->action != "check-recent")
      $rcmail->output->add_script("update_ui({'mail_storage_online': " . json_encode($mail_storage_online) . "});", "docready");
    else
      $rcmail->output->command("plugin.mail_storage_monitoring.update_ui", array("mail_storage_online" => $mail_storage_online));

    if (!$mail_storage_online)
    {
      $rcmail->output->show_message(
        $this->gettext("errormsg"),
        $type="error", 
        null, 
        true,
        $rcmail->task == "login" ? -1 : ($rcmail->config->get("refresh_interval", 60) - 1)
      );
    }
  }
}
