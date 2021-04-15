# upload-bundle
1. installation
```
composer require kematjaya/upload-bundle
```
2. add to bundles.php
```
...
Kematjaya\UploadBundle\UploadBundle::class => ['all' => true]
...
```
6. Set Upload directory
```
// config/packages/upload.yaml
upload:
    uploads_dir: '%kernel.project_dir%/public/uploads'
```
6. use KmjFileType in form
```
...
use Kematjaya\UploadBundle\Type\KmjFileType;
...
...
public function buildForm(FormBuilderInterface $builder, array $options)
{
    ...
    $builder->add('attachment', KmjFileType::class, [
      'label' => 'attachment'
    ]);
    
    // or add additional directory inside upload dir
    $builder->add('attachment', KmjFileType::class, [
      'label' => 'attachment',
      'additional_path' => 'foo'
    ]);
    ...
}
```
7. update your database schema
```
php bin/console doctrine:schema:update --force
```
8. add form theme in config/packages/twig.yml
```
twig:
    form_themes: [
        '@Upload/fields.html.twig',
        ......
    ]
```
