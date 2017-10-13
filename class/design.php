<?php

class Design
{
    function Header()
    {
        include 'ux/header.php';
    }
    
    function Footer()
    {
        include 'ux/footer.php';
    }
    
    function Message($message, $type = 'info')
    {
        return '
            <div class="alert alert-'.$type.'">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                '.$message.'
            </div
        ';
    }
}