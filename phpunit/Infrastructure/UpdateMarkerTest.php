<?php
namespace Bga\Games\PyramidoCannonFodder\Infrastructure;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Marker.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class UpdateMarkerTest extends TestCase{
    protected ?UpdateMarker $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected int $player_id = 77;
    protected string $quarry_index = 'quarry-2';
    protected int $stage = 4;
    protected int $horizontal = 19;
    protected int $vertical = 18;
    protected int $rotation = 3;
    protected array $marker_specification = ['stage' => 0, 'horizontal' => 19, 'vertical' => 18, 'rotation' => 3, ];

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = UpdateMarker::create($this->mock_cards);
    }

}
?>
