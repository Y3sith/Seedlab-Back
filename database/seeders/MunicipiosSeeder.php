<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipio;

class MunicipiosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $municipiosPorDepartamento = [
            1 => ['Leticia', 'Puerto Nariño'],
            2 => ['Abejorral', 'Abriaquí', 'Alejandría', 'Amagá', 'Amalfi', 'Andes', 'Angelópolis', 'Angostura', 'Anorí', 'Anza', 'Apartadó', 'Arboletes', 'Argelia', 'Armenia', 'Barbosa', 'Bello', 'Belmira', 'Betania', 'Betulia', 'Briceño', 'Buriticá', 'Cáceres', 'Caicedo', 'Caldas', 'Campamento', 'Cañasgordas', 'Caracolí', 'Caramanta', 'Carepa', 'Carolina del Príncipe', 'Caucasia', 'Chigorodó', 'Cisneros', 'Ciudad Bolívar', 'Cocorná', 'Concordia', 'Copacabana', 'Dabeiba', 'Don Matías', 'Ebéjico', 'El Bagre', 'Entrerríos', 'Envigado', 'Fredonia', 'Frontino', 'Giraldo', 'Girardota', 
            'Gómez Plata', 'Granada', 'Guadalupe', 'Guarne', 'Guatapé', 'Heliconia', 'Hispania', 'Itagüí', 
            'Ituango', 'Jardín', 'Jericó', 'La Ceja', 'La Estrella', 'La Pintada', 'La Unión', 
            'Liborina', 'Maceo', 'Marinilla', 'Medellín', 'Montebello', 'Murindó', 'Mutatá', 
            'Nariño', 'Necoclí', 'Nechí', 'Olaya', 'Peñol', 'Peque', 'Pueblorrico', 'Puerto Berrío', 'Puerto Nare', 'Puerto Triunfo', 
            'Remedios', 'Retiro', 'Rionegro', 'Sabanalarga', 'Sabaneta', 'Salgar', 'San Andrés de Cuerquía', 'San Carlos', 'San Francisco', 
            'San Jerónimo', 'San José de la Montaña', 'San Juan de Urabá', 'San Luis', 'San Pedro de los Milagros', 'San Pedro de Urabá', 
            'San Rafael', 'San Roque', 'San Vicente', 'Santa Bárbara', 'Santa Fe de Antioquia', 'Santa Rosa de Osos', 'Santo Domingo', 
            'Segovia', 'Sonsón', 'Sopetrán', 'Támesis', 'Tarazá', 'Tarso', 'Titiribí', 'Toledo', 'Turbo', 'Uramita', 'Urrao', 'Valdivia', 
            'Valparaíso', 'Vegachí', 'Venecia', 'Vigía del Fuerte', 'Yalí', 'Yarumal', 'Yolombó', 'Yondó', 'Zaragoza'],
            3 => ['Arauca', 'Arauquita', 'Cravo Norte', 'Fortul', 'Puerto Rondón', 'Saravena', 'Tame'],
            4 => ['Baranoa', 'Barranquilla', 'Campo de la Cruz', 'Candelaria', 'Galapa', 'Juan de Acosta', 'Luruaco', 'Malambo', 'Manatí', 'Palmar de Varela', 'Piojó', 'Polonuevo', 'Ponedera', 'Puerto Colombia', 
            'Repelón', 'Sabanagrande', 'Sabanalarga', 'Santa Lucía', 'Santo Tomás', 'Soledad', 'Suán', 'Tubará', 'Usiacurí'],
            5 => ['Achí', 'Altos del Rosario', 'Arenal', 'Arjona', 'Arroyohondo', 'Barranco de Loba', 
            'Calamar', 'Cantagallo', 'Cartagena de Indias', 'Cicuco', 'Clemencia', 'Córdoba', 'El Carmen de Bolívar', 
            'El Guamo', 'El Peñón', 'Hatillo de Loba', 'Magangué', 'Mahates', 'Margarita', 'María la Baja', 'Mompós', 
            'Montecristo', 'Morales', 'Norosí', 'Pinillos', 'Regidor', 'Río Viejo', 'San Cristóbal', 'San Estanislao', 
            'San Fernando', 'San Jacinto', 'San Jacinto del Cauca', 'San Juan Nepomuceno', 'San Martín de Loba', 'San Pablo', 
            'Santa Catalina', 'Santa Rosa', 'Santa Rosa del Sur', 'Simití', 'Soplaviento', 'Talaigua Nuevo', 'Tiquisio', 'Turbaco', 
            'Turbaná', 'Villanueva', 'Zambrano'],
            6 => ['Almeida', 'Aquitania', 'Arcabuco', 'Belén', 
            'Berbeo', 'Beteitiva', 'Boavita', 'Boyacá', 'Briceño', 
            'Buenavista', 'Busbanzá', 'Caldas', 'Campohermoso', 'Cerinza', 
            'Chinavita', 'Chiquinquirá', 'Chiscas', 'Chita', 'Chitaraque', 'Chivatá', 
            'Chivor', 'Ciénega', 'Cómbita', 'Coper', 'Corrales', 'Covarachía', 'Cubará', 
            'Cucaita', 'Cuítiva', 'Duitama', 'El Cocuy', 'El Espino', 'Firavitoba', 'Floresta', 
            'Gachantivá', 'Gámeza', 'Garagoa', 'Guacamayas', 'Guateque', 'Guayatá', 'Guican', 'Iza', 
            'Jenesano', 'Jericó', 'La Capilla', 'La Uvita', 'La Victoria', 'Labranzagrande', 'Macanal', 
            'Maripí', 'Miraflores', 'Mongua', 'Monguí', 'Moniquirá', 'Motavita', 'Muzo', 'Nobsa', 'Nuevo Colón', 
            'Oicatá', 'Otanche', 'Pachavita', 'Páez', 'Paipa', 'Pajarito', 'Panqueba', 'Pauna', 'Paya', 'Paz de Río', 
            'Pesca', 'Pisba', 'Puerto Boyacá', 'Quípama', 'Ramiriquí', 'Ráquira', 'Rondón', 'Saboyá', 'Sáchica', 'Samacá', 
            'San Eduardo', 'San José de Pare', 'San Luis de Gaceno', 'San Mateo', 'San Miguel de Sema', 'San Pablo de Borbur', 'Santa María', 
            'Santa Rosa de Viterbo', 'Santa Sofía', 'Santana', 'Sativanorte', 'Sativasur', 'Siachoque', 'Soatá', 'Socha', 'Socotá', 'Sogamoso', 'Somondoco', 
            'Sora', 'Soracá', 'Sotaquirá', 'Susacón', 'Sutamarchán', 'Sutatenza', 'Tasco', 'Tenza', 'Tibaná', 'Tibasosa', 'Tinjacá', 'Tipacoque', 'Toca', 'Togüí', 
            'Tópaga', 'Tota', 'Tunja', 'Tununguá', 'Turmequé', 'Tuta', 'Tutazá', 'Umbita', 'Ventaquemada', 'Villa de Leyva', 'Viracachá', 'Zetaquira'],
            7 => ['Aguadas', 'Anserma', 'Aranzazu', 'Belalcázar', 'Chinchiná', 'Filadelfia', 
            'La Dorada', 'La Merced', 'Manizales', 'Manzanares', 'Marmato', 'Marquetalia', 'Marulanda', 
            'Neira', 'Norcasia', 'Pácora', 'Palestina', 'Pensilvania', 'Riosucio', 'Risaralda', 'Salamina',
             'Samaná', 'San José', 'Supía', 'Victoria', 'Villamaría', 'Viterbo'],
            8=> ['Albania', 'Belén de los Andaquíes', 'Cartagena del Chairá', 'Curillo', 'El Doncello', 
            'El Paujil', 'Florencia', 'La Montañita', 'Milán', 'Morelia', 'Puerto Rico', 'San José del Fragua', 
            'San Vicente del Caguán', 'Solano', 'Solita', 'Valparaíso'],
            9 => ['Aguazul', 'Chámeza', 'Hato Corozal', 'La Salina', 'Maní', 'Monterrey', 
            'Nunchía', 'Orocué', 'Paz de Ariporo', 'Pore', 'Recetor', 'Sabanalarga', 'Sácama', 
            'San Luis de Palenque', 'Támara', 'Tauramena', 'Trinidad', 'Villanueva', 'Yopal'],
            10 => ['Almaguer', 'Argelia', 'Balboa', 'Bolívar', 'Buenos Aires', 'Cajibío', 
            'Caldono', 'Caloto', 'Corinto', 'El Tambo', 'Florencia', 'Guachené', 'Guapi', 'Inzá', 
            'Jambaló', 'La Sierra', 'La Vega', 'López de Micay', 'Mercaderes', 'Miranda', 'Morales', 
            'Padilla', 'Páez', 'Patía', 'Piamonte', 'Piendamó', 'Popayán', 'Puerto Tejada', 'Puracé', 
            'Rosas', 'San Sebastián', 'Santander de Quilichao', 'Santa Rosa', 'Silvia', 'Sotara', 'Suárez', 
            'Sucre', 'Timbío', 'Timbiquí', 'Toribio', 'Totoró', 'Villa Rica'],
            11 => ['Aguachica', 'Agustín Codazzi', 'Astrea', 'Becerril', 
            'Bosconia', 'Chimichagua', 'Chiriguaná', 'Curumaní', 'El Copey', 
            'El Paso', 'Gamarra', 'González', 'La Gloria', 'La Jagua de Ibirico', 'La Paz', 
            'Manaure Balcón del Cesar', 'Pailitas', 'Pelaya', 'Pueblo Bello', 'Río de Oro', 'San Alberto', '
            San Diego', 'San Martín', 'Tamalameque', 'Valledupar'],
            12 => ['Acandí', 'Alto Baudó', 'Bagadó', 'Bahía Solano', 
            'Bajo Baudó', 'Bojayá', 'Cértegui', 'Condoto', 'El Cantón del San Pablo', 
            'El Carmen de Atrato', 'El Carmen del Darién', 'Istmina', 'Juradó', 'Lloró', 'Medio Atrato', 
            'Medio Baudó', 'Medio San Juan', 'Nóvita', 'Nuquí', 'Quibdó', 'Río Iró', 'Río Quito', 'Riosucio', 
            'San José del Palmar', 'Sipí', 'Tadó', 'Unguía', 'Unión Panamericana'],
            13 => ['Ayapel', 'Buenavista', 'Canalete', 'Cereté', 'Chimá', 'Chinú', 'Ciénaga de Oro', 
            'Cotorra', 'La Apartada', 'Lorica', 'Los Córdobas', 'Momil', 'Montelíbano', 'Montería', 'Moñitos', 
            'Planeta Rica', 'Pueblo Nuevo', 'Puerto Escondido', 'Puerto Libertador', 'Purísima', 'Sahagún', 'San Andrés de Sotavento', 
            'San Antero', 'San Bernardo del Viento', 'San Carlos', 'San José de Uré', 'San Pelayo', 'Tierralta', 'Tuchín', 'Valencia'],
            14 => ['Agua de Dios', 'Albán', 'Anapoima', 'Anolaima', 'Apulo', 'Arbeláez', 
            'Beltrán', 'Bituima', 'Bojacá', 'Cabrera', 'Cachipay', 'Cajicá', 'Caparrapí', 'Cáqueza', 
            'Carmen de Carupa', 'Chaguaní', 'Chía', 'Chipaque', 'Choachí', 'Chocontá', 'Cogua', 'Cota', 'Cucunubá', 
            'El Colegio', 'El Peñón', 'El Rosal', 'Facatativá', 'Fomeque', 'Fosca', 'Funza', 'Fúquene', 'Fusagasugá', 'Gachalá', 
            'Gachancipá', 'Gachetá', 'Gama', 'Girardot', 'Granada', 'Guachetá', 'Guaduas', 'Guasca', 'Guataquí', 'Guatavita', 'Guayabal de Síquima', 
            'Guayabetal', 'Gutiérrez', 'Jerusalén', 'Junín', 'La Calera', 'La Mesa', 'La Palma', 'La Peña', 'La Vega', 'Lenguazaque', 'Machetá', 'Madrid', 
            'Manta', 'Medina', 'Mosquera', 'Nariño', 'Nemocón', 'Nilo', 'Nimaima', 'Nocaima', 'Pacho', 'Paime', 'Pandi', 'Paratebueno', 'Pasca', 'Puerto Salgar', 
            'Pulí', 'Quebradanegra', 'Quetame', 'Quipile', 'Ricaurte', 'San Antonio de Tequendama', 'San Bernardo', 'San Cayetano', 'San Francisco', 
            'San Juan de Rioseco', 'Sasaima', 'Sesquilé', 'Sibaté', 'Silvania', 'Simijaca', 'Soacha', 'Sopó', 'Subachoque', 'Suesca', 'Supatá', 'Susa', '
            Sutatausa', 'Tabio', 'Tausa', 'Tena', 'Tenjo', 'Tibacuy', 'Tibirita', 'Tocaima', 'Tocancipá', 'Topaipí', 'Ubalá', 'Ubaque', 'Ubaté', 'Une', 'Útica', 
            'Venecia', 'Vergara', 'Vianí', 'Villagómez', 'Villapinzón', 'Villeta', 'Viotá', 'Yacopí', 'Zipacón', 'Zipaquirá'],
            16 => ['Barranco Minas', 'Cacahual', 'Inírida', 'La Guadalupe', 'Mapiripana', 'Morichal', 'Pana Pana', 'Puerto Colombia', 'San Felipe'],
            17 => ['Calamar', 'El Retorno', 'Miraflores', 'San José del Guaviare'],
            18 => ['Albania', 'Barrancas', 'Dibulla', 
            'Distracción', 'El Molino', 'Fonseca', 'Hatonuevo', 
            'La Jagua del Pilar', 'Maicao', 'Manaure', 'Riohacha', 
            'San Juan del Cesar', 'Uribia', 'Urumita', 'Villanueva'],
            19 => ['Algarrobo', 'Aracataca', 'Ariguaní', 'Cerro de San Antonio', 'Chibolo', 'Chibolo', 'Ciénaga', 'Concordia', 'El Banco', 'El Piñón', 'El Retén', 'Fundación', 'Guamal', 'Nueva Granada', 'Pedraza', 'Pijiño del Carmen', 'Pivijay', 'Plato', 'Pueblo Viejo', 'Remolino', 'Sabanas de San Ángel', 'Salamina', 'San Sebastián de Buenavista', 'San Zenón', 'Santa Ana', 'Santa Bárbara de Pinto', 'Santa Marta', 'Sitionuevo', 'Tenerife', 'Zapayán', 'Zona Bananera'],
            20 => ['Acacías', 'Barranca de Upía', 'Cabuyaro', 'Castilla la Nueva', 'Cubarral', 'Cumaral', 'El Calvario', 'El Castillo', 'El Dorado', 'Fuente de Oro', 'Granada', 'Guamal', 'La Macarena', 'La Uribe', 'Lejanías', 'Mapiripán', 'Mesetas', 'Puerto Concordia', 'Puerto Gaitán', 'Puerto Lleras', 'Puerto López', 'Puerto Rico', 'Restrepo', 'San Carlos de Guaroa', 'San Juan de Arama', 'San Juanito', 'San Martín', 'Uribe', 'Villavicencio', 'Vista Hermosa'],
            21 => ['Aldana', 'Ancuya', 'Arboleda', 'Barbacoas', 'Belén', 'Buesaco', 'Chachagüí', 'Colón', 'Consacá', 'Contadero', 'Córdoba', 'Cuaspud', 'Cumbal', 'Cumbitara', 'El Charco', 'El Peñol', 'El Rosario', 'El Tablón de Gómez', 'El Tambo', 'Francisco Pizarro', 'Funes', 'Guachucal', 'Guaitarilla', 'Gualmatán', 'Iles', 'Imués', 'Ipiales', 'La Cruz', 'La Florida', 'La Llanada', 'La Tola', 'La Unión', 'Leiva', 'Linares', 'Los Andes', 'Magüí', 'Mallama', 'Mosquera', 'Nariño', 'Olaya Herrera', 'Ospina', 'Pasto', 'Policarpa', 'Potosí', 'Providencia', 'Puerres', 'Pupiales', 'Ricaurte', 'Roberto Payán', 'Samaniego', 'San Bernardo', 'San Lorenzo', 'San Pablo', 'San Pedro de Cartago', 'Sandoná', 'Santa Bárbara', 'Santacruz', 'Sapuyes', 'Sotomayor', 'Taminango', 'Tangua', 'Tumaco', 'Túquerres', 'Yacuanquer'],
            22=> ['Ábrego', 'Arboledas', 'Bochalema', 'Bucarasica', 'Cáchira', 'Cácota', 'Chinácota', 'Chitagá', 'Convención', 'Cúcuta', 'Cucutilla', 'Duranía', 'El Carmen', 'El Tarra', 'El Zulia', 'Gramalote', 'Hacarí', 'Herrán', 'La Esperanza', 'La Playa', 'Labateca', 'Los Patios', 'Lourdes', 'Mutiscua', 'Ocaña', 'Pamplona', 'Pamplonita', 'Puerto Santander', 'Ragonvalia', 'Salazar', 'San Calixto', 'San Cayetano', 'Santiago', 'Sardinata', 'Silos', 'Teorama', 'Tibú', 'Toledo', 'Villa Caro', 'Villa del Rosario'],
            23=> ['Colón', 'Mocoa', 'Orito', 'Puerto Asís', 'Puerto Caicedo', 'Puerto Guzmán', 'Puerto Leguízamo', 'San Francisco', 'San Miguel', 'Santiago', 'Sibundoy', 'Valle del Guamuez', 'Villagarzón'],
            24=> ['Armenia', 'Buenavista', 'Calarcá', 'Circasia', 'Córdoba', 'Filandia', 'Génova', 'La Tebaida', 'Montenegro', 'Pijao', 'Quimbaya', 'Salento'],
            25 => ['Apía', 'Balboa', 'Belén de Umbría', 'Dosquebradas', 'Guática', 'La Celia', 'La Virginia', 'Marsella', 'Mistrató', 'Pereira', 'Pueblo Rico', 'Quinch'],
            26=> ['Providencia y Santa Catalina Islas', 'San Andrés'],
            27=> ['Aguada', 'Albania', 'Aratoca', 'Barbosa', 'Barichara', 'Barrancabermeja', 'Betulia', 'Bolívar', 'Bucaramanga', 'Cabrera', 'California', 'Capitanejo', 'Carcasí', 'Cepitá', 'Cerrito', 'Charalá', 'Charta', 'Chima', 'Chipatá', 'Cimitarra', 'Concepción', 'Confines', 'Contratación', 'Coromoro', 'Curití', 'El Carmen de Chucurí', 'El Guacamayo', 'El Peñón', 'El Playón', 'Encino', 'Enciso', 'Florián', 'Floridablanca', 'Galán', 'Gámbita', 'Girón', 'Guaca', 'Guadalupe', 'Guapotá', 'Guavatá', 'Güepsa', 'Hato', 'Jesús María', 'Jordán', 'La Belleza', 'La Paz', 'Landázuri', 'Lebrija', 'Los Santos', 'Macaravita', 'Málaga', 'Matanza', 'Mogotes', 'Molagavita', 'Ocamonte', 'Oiba', 'Onzaga', 'Palmar', 'Palmas del Socorro', 'Páramo', 'Piedecuesta', 'Pinchote', 'Puente Nacional', 'Puerto Parra', 'Puerto Wilches', 'Rionegro', 'Sabana de Torres', 'San Andrés', 'San Benito', 'San Gil', 'San Joaquín', 'San José de Miranda', 'San Miguel', 'San Vicente de Chucurí', 'Santa Bárbara', 'Santa Helena del Opón', 'Simacota', 'Socorro', 'Suaita', 'Sucre', 'Suratá', 'Tona', 'Valle de San José', 'Vélez', 'Vetas', 'Villanueva', 'Zapatoca'],
            28=> ['Buenavista', 'Caimito', 'Chalán', 'Coloso', 'Corozal', 'Coveñas', 'El Roble', 'Galeras', 'Guaranda', 'La Unión', 'Los Palmitos', 'Majagual', 'Morroa', 'Ovejas', 'Palmito', 'Sampués', 'San Benito Abad', 'San Juan de Betulia', 'San Marcos', 'San Onofre', 'San Pedro', 'Sincé', 'Sincelejo', 'Sucre', 'Toluviejo'],
            29=> ['Alpujarra', 'Alvarado', 'Ambalema', 'Anzoátegui', 'Armero', 'Ataco', 'Cajamarca', 'Carmen de Apicalá', 'Casabianca', 'Chaparral', 'Coello', 'Coyaima', 'Cunday', 'Dolores', 'Espinal', 'Falán', 'Flandes', 'Fresno', 'Guamo', 'Herveo', 'Honda', 'Ibagué', 'Icononzo', 'Lérida', 'Líbano', 'Mariquita', 'Melgar', 'Murillo', 'Natagaima', 'Ortega', 'Palocabildo', 'Piedras', 'Planadas', 'Prado', 'Purificación', 'Rioblanco', 'Roncesvalles', 'Rovira', 'Saldaña', 'San Antonio', 'San Luis', 'Santa Isabel', 'Suárez', 'Valle de San Juan', 'Venadillo', 'Villahermosa', 'Villarrica'],
            30=> ['Alcalá', 'Andalucía', 'Ansermanuevo', 'Argelia', 'Bolívar', 'Buenaventura', 'Buga', 'Bugalagrande', 'Caicedonia', 'Calima', 'Cali', 'Candelaria', 'Cartago', 'Dagua', 'El Águila', 'El Cairo', 'El Cerrito', 'El Dovio', 'Florida', 'Ginebra', 'Guacarí', 'Jamundí', 'La Cumbre', 'La Unión', 'La Victoria', 'Obando', 'Palmira', 'Pradera', 'Restrepo', 'Riofrío', 'Roldanillo', 'San Pedro', 'Sevilla', 'Toro', 'Trujillo', 'Tuluá', 'Ulloa', 'Versalles', 'Vijes', 'Yotoco', 'Yumbo', 'Zarzal'],
            31=> ['Carurú', 'Mitú', 'Taraira', 'Pacoa', 'Papunahua'],
            32=> ['Cumaribo', 'La Primavera', 'Puerto Carreño', 'Santa Rosalía'] 
        ];
        foreach ($municipiosPorDepartamento as $idDepartamento => $municipios) {
            foreach ($municipios as $nombreMunicipio) {
                Municipio::create([
                    'nombre' => $nombreMunicipio,
                    'id_departamento' => $idDepartamento,
                ]);
            }
        }
    }
}
