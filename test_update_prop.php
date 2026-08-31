<?php
$property = \App\Models\Property::find(29);
echo "Original Name: " . json_encode($property->name) . "\n";
echo "Original Description: " . json_encode($property->description) . "\n";

$data = [
    'name' => ['en' => 'Bahi Ajman Palace Hotel Edited'],
    'description' => ['en' => 'New test description'],
];

$property->update($data);

$updated = \App\Models\Property::find(29);
echo "Updated Name: " . json_encode($updated->name) . "\n";
echo "Updated Description: " . json_encode($updated->description) . "\n";
