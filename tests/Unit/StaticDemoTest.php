<?php

test('the bundled demo has only local tour links and read only forms', function () {
    $directory = dirname(__DIR__, 2).'/public/demo-publica';
    $pages = glob($directory.'/*.html');

    expect(count($pages))->toBeGreaterThan(3);

    foreach ($pages as $page) {
        $html = file_get_contents($page);
        expect($html)->not->toContain('127.0.0.1', 'name="_token"', 'name="csrf-token"', 'asignarCliente(');

        $document = new DOMDocument;
        $previousErrors = libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        foreach ($document->getElementsByTagName('form') as $form) {
            expect(strtolower($form->getAttribute('method') ?: 'get'))->toBe('get');
        }

        foreach ($document->getElementsByTagName('a') as $link) {
            $href = $link->getAttribute('href');

            if (str_starts_with($href, '#') || $href === '/login') {
                continue;
            }

            expect($href)->toStartWith('/demo-publica/');
            expect(is_file(dirname(__DIR__, 2).'/public'.parse_url($href, PHP_URL_PATH)))->toBeTrue();
        }
    }
});
