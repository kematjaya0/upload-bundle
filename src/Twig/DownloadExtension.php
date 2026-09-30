<?php

namespace Kematjaya\UploadBundle\Twig;

use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DownloadExtension extends AbstractExtension
{
    public const LABEL_FILENAME = 'filename';
    public const LABEL_DEFAULT = 'default';

    public function __construct(
        private readonly Environment $twig,
        private readonly DocumentRepositoryInterface $repository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('download_link', [$this, 'downloadLink'], ['is_safe' => ['html']]),
        ];
    }

    public function downloadLink(?string $id = null, array $options = []): ?string
    {
        $options['attr']['class'] ??= 'btn btn-sm btn-outline-success';
        $options['icon'] ??= '<span class="fa fa-download"></span>';
        $options['label_type'] ??= self::LABEL_DEFAULT;
        $options['label'] ??= $this->translator->trans('download');

        if (self::LABEL_FILENAME === $options['label_type'] && null !== $id) {
            $document = $this->repository->findOneById($id);
            if (null !== $document) {
                $options['label'] = $document->getFileName();
            }
        }

        $options['attr'] = HtmlAttributes::render($options['attr']);

        return $this->twig->render('@Upload/_download.twig', [
            'data' => $id, 'options' => $options,
        ]);
    }
}
