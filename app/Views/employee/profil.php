<?php
$content = view('employee/content/profil_content', $data);
echo view('employee/layout', [
    'title' => 'Mon profil',
    'content' => $content,
    'active' => 'profil',
    'css' => ['/css/employee-profil.css'],
]);
