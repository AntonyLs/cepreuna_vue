<?php

use Illuminate\Database\Seeder;
use App\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Nolberto Julian',
            'email' => 'ncalisaya@unap.edu.pe',
            'password' => bcrypt('1ncalisaya05'),
            'paterno' => 'Calisaya',
            'materno' => 'Quisocala',
            'dni' => '00000000',
        ]);
        // User::create([
        //     'name' => 'Lucy Marleny',
        //     'email' => 'lmventura@cepreuna.edu.pe',
        //     'password' => bcrypt('75lmventura0'),
        //     'paterno' => 'Ventura',
        //     'materno' => 'Huanca',
        //     'dni' => '75697530',
        // ]);
        // User::create([
        //     'name' => 'Luz Amanda',
        //     'email' => 'laaguirre@cepreuna.edu.pe',
        //     'password' => bcrypt('02laaguirre82'),
        //     'paterno' => 'Aguirre',
        //     'materno' => 'Flores',
        //     'dni' => '0236826',
        // ]);
        // User::create([
        //     'name' => 'Adelaida',
        //     'email' => 'agviza@cepreuna.edu.pe',
        //     'password' => bcrypt('41agviza45'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Rolando Fisber',
        //     'email' => 'rfalosilla@cepreuna.edu.pe',
        //     'password' => bcrypt('25rfalosilla87'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Wilson',
        //     'email' => 'wmachaca@cepreuna.edu.pe',
        //     'password' => bcrypt('89wmachaca5'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Miguel Angel',
        //     'email' => 'marivas@cepreuna.edu.pe',
        //     'password' => bcrypt('98marivas01'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Lee',
        //     'email' => 'jlbanegasr@cepreuna.edu.pe',
        //     'password' => bcrypt('55jlbanegasr01'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'William Edward',
        //     'email' => 'wezenteno@cepreuna.edu.pe',
        //     'password' => bcrypt('89wezenteno5'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Alvaro',
        //     'email' => 'avilca@cepreuna.edu.pe',
        //     'password' => bcrypt('4avilca98'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Naysha Sharon',
        //     'email' => 'nsvillanueva@cepreuna.edu.pe',
        //     'password' => bcrypt('96nsvillanueva58'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Justo Miguel',
        //     'email' => 'jmquispe@cepreuna.edu.pe',
        //     'password' => bcrypt('98jmquispe5'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Serapio Cecilio',
        //     'email' => 'sccalcina@cepreuna.edu.pe',
        //     'password' => bcrypt('98sccalcina25'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Marleni',
        //     'email' => 'myvalencia@cepreuna.edu.pe',
        //     'password' => bcrypt('99myvalencia12'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Noemi Tania',
        //     'email' => 'nttrujillo@cepreuna.edu.pe',
        //     'password' => bcrypt('2nttrujillo57'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Jorge',
        //     'email' => 'jrosales@cepreuna.edu.pe',
        //     'password' => bcrypt('88jrosales5'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Julia Eva',
        //     'email' => 'jegarcia@cepreuna.edu.pe',
        //     'password' => bcrypt('0jegarcia96'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Luz Marina',
        //     'email' => 'lmpari@cepreuna.edu.pe',
        //     'password' => bcrypt('89lmpari541'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Rigoberto Pablo',
        //     'email' => 'rppinto@cepreuna.edu.pe',
        //     'password' => bcrypt('69rppinto25'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Lucio',
        //     'email' => 'leflores@cepreuna.edu.pe',
        //     'password' => bcrypt('5leflores02'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);
        // User::create([
        //     'name' => 'Eloy',
        //     'email' => 'enina@cepreuna.edu.pe',
        //     'password' => bcrypt('88enina20'),
        //     'paterno' => 'Quispe',
        //     'materno' => 'Machaca',
        //     'dni' => '46552973',
        // ]);




        // User::create([
        //     'name' => 'Miguel',
        //     'email' => 'msgonzales@cepreuna.edu.pe',
        //     'password' => bcrypt('28msgonzales24'),
        //     'paterno' => 'Gonzales',
        //     'materno' => 'de la Riva',
        //     'dni' => '70289942',
        // ]);

        // User::create([
        //     'name' => 'Maritza Brígida',
        //     'email' => 'mbfarfan@cepreuna.edu.pe',
        //     'password' => bcrypt('44mbfarfanc15'),
        //     'paterno' => 'Farfán',
        //     'materno' => 'Canaza',
        //     'dni' => '44155920',
        // ]);

        // User::create([
        //     'name' => 'Piedad Cecilia',
        //     'email' => 'pcmaquera@cepreuna.edu.pe',
        //     'password' => bcrypt('76pcmaquera81*'),
        //     'paterno' => 'Maquera',
        //     'materno' => 'Mamani',
        //     'dni' => '76814584',
        // ]);
        // User::create([
        //     'name' => 'Emilin Liliam',
        //     'email' => 'elmamani@cepreuna.edu.pe',
        //     'password' => bcrypt('70elmaman130'),
        //     'paterno' => 'Mamani ',
        //     'materno' => 'Quispe',
        //     'dni' => '70308410',
        // ]);

        // User::create([
        //     'name' => 'Miriam Zayda',
        //     'email' => 'mzdiaz@cepreuna.edu.pe',
        //     'password' => bcrypt('dmzdiaz7009'),
        //     'paterno' => 'Diaz',
        //     'materno' => 'Calatayud',
        //     'dni' => '70093722',
        // ]);

        // User::create([
        //     'name' => 'Jimena Cintia',
        //     'email' => 'jcusedo@cepreuna.edu.pe',
        //     'password' => bcrypt('ujcusedo48'),
        //     'paterno' => 'Usedo',
        //     'materno' => 'Fernández',
        //     'dni' => '48678051',
        // ]);

        // User::create([
        //     'name' => 'Igor Luis',
        //     'email' => 'ilzapana@cepreuna.edu.pe',
        //     'password' => bcrypt('zilzapana40'),
        //     'paterno' => 'Zapana',
        //     'materno' => 'Yerba',
        //     'dni' => '40202902',
        // ]);

        // User::create([
        //     'name' => 'Lenin',
        //     'email' => 'llnieto@cepreuna.edu.pe',
        //     'password' => bcrypt('7ncnieto75'),
        //     'paterno' => 'Nieto',
        //     'materno' => 'Calle',
        //     'dni' => '70342275',
        // ]);

        // User::create([
        //     'name' => 'Miriam Beatriz',
        //     'email' => 'mbyana@cepreuna.edu.pe',
        //     'password' => bcrypt('70mbyanaYL'),
        //     'paterno' => 'Yana',
        //     'materno' => 'Livisi',
        //     'dni' => '70111959',
        // ]);

        // User::create([
        //     'name' => 'Guadalupe del Carmen',
        //     'email' => 'gdcpariapaza@cepreuna.edu.pe',
        //     'password' => bcrypt('gdcpariapaza8907'),
        //     'paterno' => 'Pariapaza',
        //     'materno' => 'Ramos',
        //     'dni' => '70316089',
        // ]);

        // User::create([
        //     'name' => 'Jakeline Magaly',
        //     'email' => 'jmarias@cepreuna.edu.pe',
        //     'password' => bcrypt('ajmarias7027'),
        //     'paterno' => 'Arias',
        //     'materno' => 'Chino',
        //     'dni' => '70298527',
        // ]);

        // User::create([
        //     'name' => 'Wilson Jose',
        //     'email' => 'wjpampa@cepreuna.edu.pe',
        //     'password' => bcrypt('34wjpampa39'),
        //     'paterno' => 'Pampa',
        //     'materno' => 'Vilca',
        //     'dni' => '43113239',
        // ]);

        // User::create([
        //     'name' => 'Sheyla Danitza',
        //     'email' => 'sdmachaca@cepreuna.edu.pe',
        //     'password' => bcrypt('72sdmachaca27'),
        //     'paterno' => 'Machaca',
        //     'materno' => 'Cazorla',
        //     'dni' => '72277124',
        // ]);

        // User::create([
        //     'name' => 'Teófilo Carlo',
        //     'email' => 'tcchambi@cepreuna.edu.pe',
        //     'password' => bcrypt('17tcchambi39'),
        //     'paterno' => 'Chambi',
        //     'materno' => 'Llanos',
        //     'dni' => '71437593',
        // ]);

        // User::create([
        //     'name' => 'Rubén',
        //     'email' => 'rmamani@cepreuna.edu.pe',
        //     'password' => bcrypt('70rmamani77'),
        //     'paterno' => 'Mamani',
        //     'materno' => 'Aguilar',
        //     'dni' => '70761577',
        // ]);

        // User::create([
        //     'name' => 'Melissa Verinia',
        //     'email' => 'mvborda@cepreuna.edu.pe',
        //     'password' => bcrypt('4457mvborda63'),
        //     'paterno' => 'Borda',
        //     'materno' => 'Puma',
        //     'dni' => '44576436',
        // ]);
    }
}
