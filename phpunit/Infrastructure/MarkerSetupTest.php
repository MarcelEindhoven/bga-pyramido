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

class MarkerSetupTest extends TestCase{
    protected ?MarkerSetup $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = MarkerSetup::create($this->mock_cards);
    }

    public function test_setup_market_pickCardForLocation_3_times() {
        // Arrange
        $this->mock_cards->expects($this->exactly(6))->method('pickCardForLocation');

        // Act
        $this->sut->setup();
        // Assert
    }
}
?>
