<?php
$content = view('employee/content/demande_content', $data);
echo view('employee/layout', [
    'title' => 'Nouvelle demande',
    'content' => $content,
    'active' => 'demande',
    'css' => ['/css/employee-demande.css'],
]);

