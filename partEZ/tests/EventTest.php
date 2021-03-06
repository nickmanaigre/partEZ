<?php

use App\Event;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class EventTest extends TestCase
{
    use DatabaseMigrations;

    public function startUp()
    {
        $this->lastEvent = Event::max('eid');
        $this->user = User::create(array('firstname' => 'Simon', 'email' => 'simon2@gmail.com'));
    }

    public function testEventInsert()
    {
        $this->startUp();
        $event = Event::create(array('name'=>'The Red Wedding', 'uid'=>$this->user->uid, 'location' => 'Winnipeg'));

        $this->seeInDatabase('events', ['name'=>'The Red Wedding']);
    }

    public function testEventRetrieve()
    {
        $this->startUp();
        $event = Event::create(array('name'=>'The Red Wedding', 'uid'=>$this->user->uid, 'location' => 'Winnipeg'));
        $event = Event::find($event->eid);

        $this->assertNotNull($event, 'Could not retrieve event');
    }

    public function testEventUpdate()
    {
        $this->startUp();

        $event = Event::create(array('name'=>'The Red Wedding', 'uid'=>$this->user->uid, 'location' => 'Winnipeg'));
        $event->location = 'Castle Frey';

        $eid = $event->eid;
        $event->save();

        $event = Event::find($eid);
        $result = $event->location == 'Castle Frey';

        $this->assertTrue($result);
    }

    public function testEventDelete()
    {
        $this->startUp();
        
        $event = Event::create(array('name'=>'The Red Wedding', 'uid'=>$this->user->uid, 'location' => 'Winnipeg'));
        $event->delete();

        $this->notSeeInDatabase('events', array('name'=>'TheRedWedding'));
    }

    protected function tearDown()
    {
        if(is_null($this->lastEvent))
        {
            $event = Event::first();
            if(!is_null($event))
            {
                $event->delete();
            }
        }
        else
        {
            Event::where('eid', '>', $this->lastEvent)->delete();   
        }

        User::where('uid', $this->user->uid)->delete();
    }
}
