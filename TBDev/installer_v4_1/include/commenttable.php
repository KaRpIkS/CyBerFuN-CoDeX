<?php
if (!defined('PUBLIC_ACCESS')) die('Fuck off - You cant access scripts directly fool !');
function get_user_ratio_image($ratio)
{
    switch ($ratio) {
        case ($ratio < 0.6): return ' <img src=pic/smilies/shit.gif alt=" Bad ratio :(" title=" Bad ratio :(">';
            break;
        case ($ratio <= 0.7): return ' <img src=pic/smilies/weep.gif alt=" Could be better" title=" Could be better">';
            break;
        case ($ratio <= 0.8): return ' <img src=pic/smilies/cry.gif alt=" Getting there!" title=" Getting there!">';
            break;
        case ($ratio <= 1.5): return ' <img src=pic/smilies/smile1.gif alt=" Good Ratio :)" title=" Good Ratio :)">';
            break;
        case ($ratio <= 2.0): return ' <img src=pic/smilies/grin.gif alt=" Great Ratio :)" title=" Great Ratio :)">';
            break;
        case ($ratio <= 3.0): return ' <img src=pic/smilies/w00t.gif alt=" Wow! :D" title=" Wow! :D">';
            break;
        case ($ratio <= 4.0): return ' <img src=pic/smilies/pimp.gif alt=" Fa-boo Ratio!" title=" Fa-boo Ratio!">';
            break;
        case ($ratio > 4.0): return ' <img src=pic/smilies/clap2.gif alt=" Great ratio :-D" title=" Great ratio :-D">';
            break;
    }
    return '';
}

function commenttable($rows)
{
global $CURUSER, $HTTP_SERVER_VARS;
// === get smilie based on ratio
    begin_main_frame();
    begin_frame();
    $count = 0;
    foreach ($rows as $row) {
        $querie = sql_query("SELECT anonymous FROM comments WHERE id =" . unsafeChar($row['id']) . "");
        $arraya = mysql_fetch_assoc($querie);
        echo("<p class=sub>#" . safeChar($row["id"]) . " by ");
       $title = (!empty($row["title"])?$row["title"]:get_user_class_name($row["class"]));
        if ($arraya['anonymous'] == 'no' && isset($row["username"])) {
            $username = $row["username"];
            $ratres = sql_query("SELECT uploaded, downloaded from users where username='$username'");
            $rat = mysql_fetch_array($ratres);
            if ($rat["downloaded"] > 0) {
                $ratio = $rat['uploaded'] / $rat['downloaded'];
                $ratio = number_format($ratio, 3);
                $color = get_ratio_color($ratio);
                if ($color)
                    $ratio = "<font color=$color>" . safeChar($ratio) . " " . get_user_ratio_image($ratio) . "</font>";
            } else
            if ($rat["uploaded"] > 0)
                $ratio = "Inf.";
            else

                $ratio = "---";
            echo("<a name=comm" . $row["id"] . " href=userdetails.php?id=" . safeChar($row["user"]) . "><b>" . safeChar($row["username"]) . "</b></a> " . ($title) . " " . ($row["donor"] == "yes" ? "<img src=pic/star.gif alt='Donor'>" : "") . ($row["warned"] == "yes" ? "<img src=" . "/pic/warned.gif alt=\"Warned\">" : "") . " Ratio: $ratio\n");
        } else if (!isset($row["username"])) {
            echo("<a name=\"comm" . $row["id"] . "\"><i>(orphaned)</i></a>\n");
        } else if ($arraya['anonymous'] == 'yes') {
            echo("<a name=\"comm" . $row["id"] . "\"><font color=blue><b>Anonymous</b></font></a>\n");
        }
        echo(" at " . display_date_time($row["added"]) . " GMT" .
            ($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "- [<a href=comment.php?action=edit&amp;cid=$row[id]>" . 'Edit' . "</a>] " : "") .
            (get_user_class() >= UC_VIP ? " - [<a href=report.php?type=Comment&id=$row[id]>Report this Comment</a>]" : "") .
            (get_user_class() >= UC_MODERATOR ? "- [<a href=comment.php?action=delete&amp;cid=$row[id]>" . 'Delete' . "</a>]" : "") .
            ($row["editedby"] && get_user_class() >= UC_MODERATOR ? " - [<a href=comment.php?action=vieworiginal&amp;cid=$row[id]>" . 'View_original' . "</a>]" : "") . "</p>\n");
        $resa = sql_query("SELECT owner, anonymous FROM torrents WHERE owner = $row[user]");
        $array = mysql_fetch_assoc($resa);
        if ($row['anonymous'] == 'yes' && $row['user'] == $array['owner']) {
            $avatar = "/pic/default_avatar.gif";
        } else {
            $avatar = ($CURUSER["avatars"] == "yes" ? safeChar($row["avatar"]) : "");
        }
        if (!$avatar)
            $avatar = "/pic/default_avatar.gif";
        begin_table(true);
        echo("<tr valign=top>\n");
        echo("<td align=center width=100 style='padding: 0px'><img width=100 src=$avatar><br />".get_reputation($row, 'comments')."</td>\n");
        echo("<td class=text>" . format_comment($row["text"]) . "</td>\n");
        echo("</tr>\n");
        end_table();
    }
    end_frame();
    end_main_frame();
}
?>