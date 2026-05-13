<?php
$content = view('employee/content/solde_content', $data);
echo view('employee/layout', [
    'title' => 'Mon solde',
    'content' => $content,
    'active' => 'solde',
    'css' => ['/css/employee-solde.css'],
]);
