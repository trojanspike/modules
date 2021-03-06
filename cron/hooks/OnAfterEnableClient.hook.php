<?php

/**
 *
 * Sentora - The open source control panel.
 * @contact developers@sentora.org
 *
 * This program (Sentora) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @package Sentora
 * @subpackage cron
 * @version $Id$
 * @author Bobby Allen - ballen@bobbyallen.me
 * @copyright (c) 2008-2014 Sentora Group - http://www.sentora.org/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 */

WriteCronFile();

function WriteCronFile() {
    global $zdbh;
    $line = "";
    $sql = "SELECT * FROM x_cronjobs WHERE ct_deleted_ts IS NULL";
    $numrows = $zdbh->query($sql);
    if ($numrows->fetchColumn() <> 0) {
        $sql = $zdbh->prepare($sql);
        $sql->execute();
        $line .= "#################################################################################" . fs_filehandler::NewLine();
        $line .= "# CRONTAB FOR ZPANEL CRON MANAGER MODULE                                         " . fs_filehandler::NewLine();
        $line .= "# Module Developed by Bobby Allen, 17/12/2009                                    " . fs_filehandler::NewLine();
        $line .= "# Automatically generated by ZPanel " . sys_versions::ShowZpanelVersion() . "      " . fs_filehandler::NewLine();
        $line .= "#################################################################################" . fs_filehandler::NewLine();
        $line .= "# WE DO NOT RECOMMEND YOU MODIFY THIS FILE DIRECTLY, PLEASE USE ZPANEL INSTEAD!  " . fs_filehandler::NewLine();
        $line .= "#################################################################################" . fs_filehandler::NewLine();

        if (sys_versions::ShowOSPlatformVersion() == "Windows") {
            $line .= "# Cron Debug infomation can be found in this file here:-                        " . fs_filehandler::NewLine();
            $line .= "# C:\WINDOWS\System32\crontab.txt                                                " . fs_filehandler::NewLine();
            $line .= "#################################################################################" . fs_filehandler::NewLine();
            $line .= "" . ctrl_options::GetSystemOption('daemon_timing') . " " . ctrl_options::GetSystemOption('php_exer') . " " . ctrl_options::GetSystemOption('daemon_exer') . "" . fs_filehandler::NewLine();
            $line .= "#################################################################################" . fs_filehandler::NewLine();
        }

        $line .= "# DO NOT MANUALLY REMOVE ANY OF THE CRON ENTRIES FROM THIS FILE, USE ZPANEL      " . fs_filehandler::NewLine();
        $line .= "# INSTEAD! THE ABOVE ENTRIES ARE USED FOR ZPANEL TASKS, DO NOT REMOVE THEM!      " . fs_filehandler::NewLine();
        $line .= "#################################################################################" . fs_filehandler::NewLine();
        while ($rowcron = $sql->fetch()) {
            //$rowclient = $zdbh->query("SELECT * FROM x_accounts WHERE ac_id_pk=" . $rowcron['ct_acc_fk'] . " AND ac_deleted_ts IS NULL")->fetch();
            $numrows = $zdbh->prepare("SELECT * FROM x_accounts WHERE ac_id_pk=:userid AND ac_deleted_ts IS NULL");
            $numrows->bindParam(':userid', $rowcron['ct_acc_fk']);
            $numrows->execute();
            $rowclient = $numrows->fetch();
            
            if ($rowclient && $rowclient['ac_enabled_in'] <> 0) {
                $line .= "# CRON ID: " . $rowcron['ct_id_pk'] . "" . fs_filehandler::NewLine();
                $line .= "" . $rowcron['ct_timing_vc'] . " " . ctrl_options::GetSystemOption('php_exer') . " " . $rowcron['ct_fullpath_vc'] . "" . fs_filehandler::NewLine();
                $line .= "# END CRON ID: " . $rowcron['ct_id_pk'] . "" . fs_filehandler::NewLine();
            }
        }

        if (fs_filehandler::UpdateFile(ctrl_options::GetSystemOption('cron_file'), 0777, $line)) {
            return true;
        } else {
            return false;
        }
    }
}

?>
