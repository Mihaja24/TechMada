<?php
$content = view('employee/content/mes_demandes_content', $data);
echo view('employee/layout', [
    'title' => 'Mes demandes',
    'content' => $content,
    'active' => 'mes_demandes',
    'css' => ['/css/employee-mes-demandes.css'],
]);
