-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS myhmsdb;
USE myhmsdb;

-- Tabla de administradores
CREATE TABLE admintb (
    username VARCHAR(50) NOT NULL,
    password VARCHAR(30) NOT NULL,
    PRIMARY KEY (username)
) ENGINE=InnoDB;

-- Tabla de doctores
CREATE TABLE doctb (
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    spec VARCHAR(50) NOT NULL,
    docFees INT(10) NOT NULL,
    PRIMARY KEY (username)
) ENGINE=InnoDB;

-- Tabla de pacientes
CREATE TABLE paciente (
    pid INT(11) NOT NULL AUTO_INCREMENT,
    fname VARCHAR(20) NOT NULL,
    lname VARCHAR(20) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    email VARCHAR(30) NOT NULL,
    contact VARCHAR(10) NOT NULL,
    password VARCHAR(30) NOT NULL,
    cpassword VARCHAR(30) NOT NULL,
    PRIMARY KEY (pid)
) ENGINE=InnoDB;

-- Tabla de citas
CREATE TABLE appointmenttb (
    pid INT(11) NOT NULL,
    ID INT(11) NOT NULL AUTO_INCREMENT,
    fname VARCHAR(20) NOT NULL,
    lname VARCHAR(20) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    email VARCHAR(30) NOT NULL,
    doctor VARCHAR(30) NOT NULL,
    docFees INT(5) NOT NULL,
    appdate DATE NOT NULL,
    apptime TIME NOT NULL,
    userStatus INT(5) NOT NULL,
    doctorStatus INT(5) NOT NULL,
    contact VARCHAR(10) NOT NULL,
    PRIMARY KEY (ID),
    FOREIGN KEY (pid) REFERENCES paciente(pid) ON DELETE CASCADE,
    FOREIGN KEY (doctor) REFERENCES doctb(username) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de prescripciones
CREATE TABLE prestb (
    ID INT(11) NOT NULL AUTO_INCREMENT,
    doctor VARCHAR(50) NOT NULL,
    pid INT(11) NOT NULL,
    appdate DATE NOT NULL,
    apptime TIME NOT NULL,
    disease VARCHAR(250) NOT NULL,
    allergy VARCHAR(250) NOT NULL,
    prescription VARCHAR(1000) NOT NULL,
    PRIMARY KEY (ID),
    FOREIGN KEY (doctor) REFERENCES doctb(username) ON DELETE CASCADE,
    FOREIGN KEY (pid) REFERENCES paciente(pid) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de contactos
CREATE TABLE contact (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    email TEXT NOT NULL,
    contact VARCHAR(10) NOT NULL,
    message VARCHAR(200) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Tabla de evaluaciones
CREATE TABLE evaluacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pid INT NOT NULL,
    doctor_username VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE,
    edad INT,
    fecha_comienzo_evaluacion DATE,
    alergias TEXT,
    control_alimenticio TEXT,
    historial_clinico TEXT,
    sintomas_gastrointestinales TEXT,
    metabolismo TEXT,
    peso_kg DECIMAL(5,2),
    longitud_cm DECIMAL(5,2),
    imc DECIMAL(5,2),
    fecha_evaluacion DATE,
    detalles TEXT,
    FOREIGN KEY (pid) REFERENCES paciente(pid) ON DELETE CASCADE,
    FOREIGN KEY (doctor_username) REFERENCES doctb(username) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de gestión del estrés
CREATE TABLE gestion_estres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pid INT NOT NULL,
    fecha DATE NOT NULL,
    nivel_estres VARCHAR(50),
    recomendaciones TEXT,
    FOREIGN KEY (pid) REFERENCES paciente(pid) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de recetas de comidas
CREATE TABLE receta_comida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pid INT NOT NULL,
    doctor_username VARCHAR(50) NOT NULL,
    comida VARCHAR(255),
    porciones INT,
    fecha DATE,
    FOREIGN KEY (pid) REFERENCES paciente(pid) ON DELETE CASCADE,
    FOREIGN KEY (doctor_username) REFERENCES doctb(username) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de objetivos logrados
CREATE TABLE objetivo_logrado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pid INT NOT NULL,
    objetivo TEXT,
    estado VARCHAR(50) CHECK (estado IN ('En progreso', 'Completado', 'Pendiente')),
    fecha DATE,
    FOREIGN KEY (pid) REFERENCES paciente(pid) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Configuración de claves primarias y auto-incremento
ALTER TABLE paciente MODIFY pid INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE appointmenttb MODIFY ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE prestb MODIFY ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE contact MODIFY id INT(11) NOT NULL AUTO_INCREMENT;

-- Finalizar transacción
COMMIT;
