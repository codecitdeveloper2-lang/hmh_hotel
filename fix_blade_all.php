<?php
// Comprehensive fix for ALL blade files with wrong folder/class references
// This maps blade variable names to the correct PHP folder + class names

$manualFixes = [
    // manage-news-and-press.blade.php
    'manage-news-and-press.blade.php' => [
        'folder'   => 'NewsAndPress',
        'create'   => 'CreateNewsAndPress',
        'view'     => 'ViewNewsAndPress',
        'edit'     => 'EditNewsAndPress',
    ],
    // manage-group-gallery.blade.php
    'manage-group-gallery.blade.php' => [
        'folder'   => 'GroupGallery',
        'create'   => 'CreateGroupGallery',
        'view'     => 'ViewGroupGallery',
        'edit'     => 'EditGroupGallery',
    ],
    // manage-gallery.blade.php
    'manage-gallery.blade.php' => [
        'folder'   => 'Gallery',
        'create'   => 'CreateGallery',
        'view'     => 'ViewGallery',
        'edit'     => 'EditGallery',
    ],
    // manage-meetings-and-events.blade.php (if exists)
    'manage-meetings-and-events.blade.php' => [
        'folder'   => 'MeetingsAndEvents',
        'create'   => 'CreateMeetingsAndEvent',
        'view'     => 'ViewMeetingsAndEvent',
        'edit'     => 'EditMeetingsAndEvent',
    ],
    // manage-contact-submissions.blade.php
    'manage-contact-submissions.blade.php' => [
        'folder'   => 'ContactSubmissions',
        'create'   => 'CreateContactSubmission',
        'view'     => 'ViewContactSubmission',
        'edit'     => 'EditContactSubmission',
    ],
    // manage-newsletter-subscribers.blade.php
    'manage-newsletter-subscribers.blade.php' => [
        'folder'   => 'NewsletterSubscribers',
        'create'   => 'CreateNewsletterSubscriber',
        'view'     => 'ViewNewsletterSubscriber',
        'edit'     => 'EditNewsletterSubscriber',
    ],
    // manage-cms-pages.blade.php
    'manage-cms-pages.blade.php' => [
        'folder'   => 'CmsPages',
        'create'   => 'CreateCmsPage',
        'view'     => 'ViewCmsPage',
        'edit'     => 'EditCmsPage',
    ],
];

$count = 0;
foreach ($manualFixes as $filename => $map) {
    $path = "resources/views/filament/pages/$filename";
    if (!file_exists($path)) continue;

    $c = file_get_contents($path);
    $original = $c;

    $folder  = $map['folder'];
    $create  = $map['create'];
    $view    = $map['view'];
    $edit    = $map['edit'];

    // Replace ANY wrong class in that file that contains View, Edit, Create
    $c = preg_replace(
        '/\\\\App\\\\Filament\\\\Pages\\\\[a-zA-Z0-9]+\\\\(View[a-zA-Z0-9]+)::getUrl/',
        "\\App\\Filament\\Pages\\$folder\\$view::getUrl",
        $c
    );
    $c = preg_replace(
        '/\\\\App\\\\Filament\\\\Pages\\\\[a-zA-Z0-9]+\\\\(Edit[a-zA-Z0-9]+)::getUrl/',
        "\\App\\Filament\\Pages\\$folder\\$edit::getUrl",
        $c
    );
    $c = preg_replace(
        '/\\\\App\\\\Filament\\\\Pages\\\\[a-zA-Z0-9]+\\\\(Create[a-zA-Z0-9]+)::getUrl/',
        "\\App\\Filament\\Pages\\$folder\\$create::getUrl",
        $c
    );

    if ($c !== $original) {
        file_put_contents($path, $c);
        echo "Fixed: $filename\n";
        $count++;
    }
}
echo "Done. Fixed $count files.";
