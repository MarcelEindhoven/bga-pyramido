<?php
namespace Bga\Games\PyramidoCannonFodder\Domain;
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
use Bga\Games\PyramidoCannonFodder\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class TopViewTest extends TestCase {
    protected ?TopView $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected array $initial41010 = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 3, 'jewels' => []];
    protected array $resurfacing = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 2, 'jewels' => [1]];

    protected function setUp(): void {
    }

    public function test_resurfacing_replaces_tile() {
        // Arrange
        $tiles = [Infrastructure\CurrentTiles::calculate_array_index($this->initial41010) => $this->initial41010];
        $this->sut = TopView::create($tiles);

        // Act
        $this->sut->resurface([$this->resurfacing]);

        // Assert
        $this->assertEquals([TopView::get_location_key($this->initial41010) => $this->resurfacing], $this->sut->get_tiles());
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
        $initial1 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 1];
        $initial4 = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 2];
        $initial4b = ['stage' => 4, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 3];
        $marker4 = ['stage' => 4, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 0];
        $marker4b = ['stage' => 4, 'horizontal' => 12, 'vertical' => 8, 'rotation' => 0];
        $location_key1010 = TopView::get_location_key($initial4);
        $pyramid_key10101 = Pyramid::get_location_key($initial1);
        $pyramid_key10104 = Pyramid::get_location_key($initial4);
        return [
            [[], []], // No tiles
          //  [[$pyramid_key10101 => $initial1], [$location_key1010 => $initial1]], // Single tile
            [[$pyramid_key10101 => $initial1, $pyramid_key10104 => $initial4], [$location_key1010 => $initial4]], // Replace tile
        ];
    }
}
