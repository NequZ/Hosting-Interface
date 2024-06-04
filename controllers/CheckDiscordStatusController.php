<?php
function is_bot_running($bot_name) {
    $output = shell_exec("ps aux | grep '$bot_name' | grep -v grep");
    if (empty($output)) {
        return false;
    }
    return true;
}

$bot_name = 'nequz_bot.py';
if (is_bot_running($bot_name)) {
    echo '<div class="alert alert-success" role="alert">
            <span class="alert-icon"><i class="fas fa-thumbs-up"></i></span>
            <strong>Success!</strong> The bot is online!
          </div>';
} else {
    echo '<div class="alert alert-danger" role="alert">
            <span class="alert-icon"><i class="fas fa-thumbs-down"></i></span>
            <strong>Danger!</strong> The bot is not online!
          </div>';
}
?>
