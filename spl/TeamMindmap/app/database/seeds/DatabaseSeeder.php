<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run(){
		Eloquent::unguard();

		$this->call('ProjectRoleTableSeeder');
        $this->call('ProjectTaskStatusTableSeeder');
        $this->call('NotifyTypeTableSeeder');
        $this->call('ProjectTaskPrioritiesTableSeeder');

		/*
		 * 以下填充类仅用于测试，请勿用于生产环境。
		 * 同时，也不在测试环境下全部加载，应该局部按需加载。
		 */
		if( Config::get('app.debug') && !App::environment('testing') ){
            $this->call('UserTableTestSeeder');
            $this->call('ProjectTableTestSeeder');
            $this->call('ProjectMemberTableTestSeeder');
            $this->call('ProjectTaskTableSeeder');
            $this->call('ProjectTaskMemberTableSeeder');
            $this->call('NotificationTableTestSeeder');
            $this->call('NotifyInboxTableTestSeeder');
            $this->call('MessagesTableTestSeeder');
            $this->call('MessageInboxsTableTestSeeder');
            $this->call('ProjectDiscussionsTableTestSeeder');
            $this->call('ProjectDiscussionFollowerTableTestSeeder');
            $this->call('ProjectDiscussionCommentsTableTestSeeder');
            $this->call('ResourceTableTestSeeder');
            $this->call('SharingTableTestSeeder');
            $this->call('SharingResourceTableTestSeeder');
            $this->call('TagTableTestSeeder');
            $this->call('SharingTagTableTestSeeder');
            $this->call('ResourceLinkTableTestSeeder');
		}
	}

}
