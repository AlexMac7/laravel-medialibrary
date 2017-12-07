<?php

namespace Spatie\MediaLibrary\ResponsiveImages;

use Spatie\MediaLibrary\Media;
use Spatie\MediaLibrary\UrlGenerator\UrlGeneratorFactory;

class ResponsiveImage
{
    /** @var string */
    public $fileName = '';

    /** @var \Spatie\MediaLibrary\Media */
    protected $media;

    public function __construct(string $fileName, Media $media)
    {
        $this->fileName = $fileName;

        $this->media = $media;
    }

    public function url(): string
    {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this->media);

        return $urlGenerator->getResponsiveImagesDirectoryUrl() . $this->fileName;
    }

    public function generatedFor(): string
    {
        $propertyParts = $this->getPropertyParts();

        array_pop($propertyParts);

        array_pop($propertyParts);

        return implode('_', $propertyParts);
    }

    public function width(): int
    {
        $propertyParts = $this->getPropertyParts();

        array_pop($propertyParts);

        return (int) last($propertyParts);
    }

    public function height(): int
    {
        $propertyParts = $this->getPropertyParts();

        return (int) last($propertyParts);
    }

    public static function register(Media $media, $fileName)
    {
        $responsiveImages = $media->responsive_images ?? [];

        $responsiveImages[] = $fileName;

        $media->responsive_images = $responsiveImages;
    
        $media->save();
    }

    protected function getPropertyParts(): array
    {
        $propertyString =  $this->stringBetween($this->fileName, '___', '.');

        return explode('_', $propertyString);
    }

    protected function stringBetween(string $subject, string $startCharacter, string $endCharacter): string
    {
        $between = strstr($subject, $startCharacter);

        $between = str_replace('___', '', $between);

        $between = strstr($between, $endCharacter, true);

        return $between;
    }
}