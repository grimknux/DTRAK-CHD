<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up() {
       $this->forge->addField([
          'id' => [
             'type' => 'INT',
             'constraint' => 5,
             'unsigned' => true,
             'auto_increment' => true,
          ],
          'mobile' => [ 
             'type' => 'int',
             'constraint' => '100',
          ],
          'username' => [
             'type' => 'VARCHAR',
             'constraint' => '80',
          ],
          'email' => [
             'type' => 'VARCHAR',
             'constraint' => '80',
          ],

       ]);
       $this->forge->addKey('id', true);
       $this->forge->createTable('testuser');
    }

    //--------------------------------------------------------------------

    public function down() {
       $this->forge->dropTable('testuser');
    }
}