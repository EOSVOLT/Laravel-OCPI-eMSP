<?php

namespace Tests\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Support\Enums\Role;

trait TestPartyRoleTrait
{
    public function getMockPartyWithRoleCPO(bool $isExternalParty): Party
    {
        $tokenMock = Mockery::mock(PartyToken::class, function (MockInterface $mock) {
            $mock->shouldReceive('setAttribute')->andReturnSelf();
            $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
            $mock->shouldReceive('getAttribute')->with('party_role_id')->andReturn(1);
            $mock->shouldReceive('getAttribute')->with('name')->andReturn('test token');
            $mock->shouldReceive('getAttribute')->with('token')->andReturn(Str::random(32));
            $mock->shouldReceive('getAttribute')->with('registered')->andReturnTrue();
        });
        $roleMock = Mockery::mock(PartyRole::class, function (MockInterface $mock) use ($tokenMock) {
            $mock->shouldReceive('setAttribute')->andReturnSelf();
            $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
            $mock->shouldReceive('getAttribute')->with('party_id')->andReturn(1);
            $mock->shouldReceive('getAttribute')->with('party')->andReturnNull();//revisit later.
            $mock->shouldReceive('getAttribute')->with('code')->andReturn('ABC');
            $mock->shouldReceive('getAttribute')->with('country_code')->andReturn('TH');
            $mock->shouldReceive('getAttribute')->with('role')->andReturn(Role::CPO);
            $mock->shouldReceive('getAttribute')->with('business_details')->andReturn([
                'name' => 'Test Business Details',
                'website' => 'www.test.com',
            ]);
            $mock->shouldReceive('getAttribute')->with('tokens')->andReturn(new Collection([$tokenMock]));
            $mock->shouldReceive('getAttribute')->with('url')->andReturn('www.test.com');
            $mock->shouldReceive('getAttribute')->with('endpoints')->andReturnNull();
        });
        $partyMock = \Mockery::mock(Party::class, function (MockInterface $mock) use ($roleMock, $isExternalParty) {
            $mock->shouldReceive('offsetExists')->andReturnTrue();
            $mock->shouldReceive('relationLoaded')->andReturnTrue();
            $mock->shouldReceive('load')->andReturnSelf();
            $mock->shouldReceive('setAttribute')->andReturnSelf();
            $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
            $mock->shouldReceive('getAttribute')->with('parent_id')->andReturnNull();
            $mock->shouldReceive('getAttribute')->with('parent')->andReturnNull();
            $mock->shouldReceive('getAttribute')->with('cpo_id')->andReturn(1);
            $mock->shouldReceive('getAttribute')->with('is_external_party')->andReturn($isExternalParty);
            $mock->shouldReceive('getAttribute')->with('code')->andReturn('TH*ABC');
            $mock->shouldReceive('getAttribute')->with('version')->andReturn('2.2.1');
            $mock->shouldReceive('getAttribute')->with('version_url')->andReturn('test-url.com');
            $mock->shouldReceive('getAttribute')->with('role_cpo')->andReturn($roleMock);
            $mock->shouldReceive('getAttribute')->with('roles')->andReturn(new Collection([$roleMock]));
        });

        return $partyMock;
    }
}