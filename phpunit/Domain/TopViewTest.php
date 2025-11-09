<?php
namespace Bga\Games\Pyramido\Domain;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Domain/Pyramid.php');
include_once(__DIR__.'/../../export/modules/php/Domain/TopView.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\Pyramido\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class TopViewTest extends TestCase {
    protected ?TopView $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected function setUp(): void {
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('get_tiles_multiple_stages')]
    public function test_get_tiles_multiple_stages($tiles, $expected_tiles) {
        // Arrange
        $this->sut = TopView::create($tiles);

        // Act
        $top_view = $this->sut->get_tiles();

        // Assert
        $this->assertEquals($expected_tiles, $top_view);
    }
    static public function get_tiles_multiple_stages(): array {
        $initial1 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 1, 'colour' => 5, 'jewels' => []];
        $initial4 = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 2, 'colour' => 5, 'jewels' => []];
        $initial4b = ['stage' => 4, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 3, 'colour' => 5, 'jewels' => []];
        $marker4 = ['stage' => 4, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 0, 'colour' => 5, 'jewels' => []];
        $marker4b = ['stage' => 4, 'horizontal' => 12, 'vertical' => 8, 'rotation' => 0, 'colour' => 5, 'jewels' => []];
        $location_key1010 = TopView::get_location_key($initial4);
        $pyramid_key10101 = Pyramid::get_location_key($initial1);
        $pyramid_key10104 = Pyramid::get_location_key($initial4);
        return [
            [[], []], // No tiles
            [[$pyramid_key10101 => $initial1], [$location_key1010 => $initial1]], // Single tile
            [[$pyramid_key10101 => $initial1, $pyramid_key10104 => $initial4], [$location_key1010 => $initial4]], // Replace tile
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('get_jewels_multiple_stages')]
    public function test_get_jewels_multiple_stages($tiles, $expected_jewels) {
        // Arrange
        $this->sut = TopView::create($tiles);

        // Act
        $top_view = $this->sut->get_jewels();

        // Assert
        $this->assertEquals($expected_jewels, $top_view);
    }
    static public function get_jewels_multiple_stages(): array {
        $initial11 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 1, 'colour' => 5, 'jewels' => []];
        $initial100 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0, 'colour' => 5, 'jewels' => [0]];
        $initial110 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0, 'colour' => 5, 'jewels' => [1]];
        $initial121 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 1, 'colour' => 5, 'jewels' => [2]];
        $initial2133 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 3, 'colour' => 5, 'jewels' => [1, 3]];
        $initial4 = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 2, 'colour' => 5, 'jewels' => []];
        $initial4b = ['stage' => 4, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 3, 'colour' => 5, 'jewels' => []];
        $marker4 = ['stage' => 4, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 0, 'colour' => 5, 'jewels' => []];
        $marker4b = ['stage' => 4, 'horizontal' => 12, 'vertical' => 8, 'rotation' => 0, 'colour' => 5, 'jewels' => []];

        $location_1010 = ['horizontal' => 10, 'vertical' => 10];
        $location_key1010 = TopView::get_location_key($location_1010);

        $location_1110 = ['horizontal' => 11, 'vertical' => 10];
        $location_key1110 = TopView::get_location_key($location_1110);

        $location_1011 = ['horizontal' => 10, 'vertical' => 11];
        $location_key1011 = TopView::get_location_key($location_1011);

        $location_1111 = ['horizontal' => 11, 'vertical' => 11];
        $location_key1111 = TopView::get_location_key($location_1111);

        $pyramid_key10101 = Pyramid::get_location_key($initial11);
        $pyramid_key10104 = Pyramid::get_location_key($initial4);
        return [
            [[], []], // No tiles
            [[$initial11], []], // No jewels
            [[$pyramid_key10101 => $initial100], [$location_key1010]], // No rotation, first jewel
            [[$pyramid_key10101 => $initial110], [$location_key1011]], // No rotation, second jewel
            [[$pyramid_key10101 => $initial121], [$location_key1111]], // Rotation 1, third jewel
            [[$pyramid_key10101 => $initial2133], [$location_key1111, $location_key1110]], // Rotation 3, second and fourth jewel
            [[$pyramid_key10101 => $initial2133, $pyramid_key10104 => $initial4], []], // Lower stage jewels hidden
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('get_colour_map_multiple_stages')]
    public function test_get_colour_map_multiple_stages($tiles, $expected_colour_map) {
        // Arrange
        $this->sut = TopView::create($tiles);

        // Act
        $top_view = $this->sut->get_colour_map();

        // Assert
        $this->assertEquals($expected_colour_map, $top_view);
    }
    static public function get_colour_map_multiple_stages(): array {
        $initial1 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 1, 'colour' => 5, 'jewels' => []];
        $initial4 = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 2, 'colour' => 5, 'jewels' => []];
        $initial4b = ['stage' => 4, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 3, 'colour' => 5, 'jewels' => []];
        $marker4 = ['stage' => 4, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 0, 'colour' => 5, 'jewels' => []];
        $marker4b = ['stage' => 4, 'horizontal' => 12, 'vertical' => 8, 'rotation' => 0, 'colour' => 5, 'jewels' => []];

        $location_1010 = ['horizontal' => 10, 'vertical' => 10];
        $location_key1010 = TopView::get_location_key($location_1010);

        $location_1011 = ['horizontal' => 10, 'vertical' => 11];
        $location_key1011 = TopView::get_location_key($location_1011);

        $location_1110 = ['horizontal' => 11, 'vertical' => 10];
        $location_key1110 = TopView::get_location_key($location_1110);

        $location_1111 = ['horizontal' => 11, 'vertical' => 11];
        $location_key1111 = TopView::get_location_key($location_1111);

        $pyramid_key10101 = Pyramid::get_location_key($initial1);
        $pyramid_key10104 = Pyramid::get_location_key($initial4);
        return [
            [[], []], // No tiles
            [[$initial1], [$location_key1010 => 5, $location_key1011 => 5, $location_key1110 => 5, $location_key1111 => 5, ]], // 4 colour locations
        ];
    }
}
