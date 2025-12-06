PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    permissions TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- members d'abord
CREATE TABLE IF NOT EXISTS members (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT,
    phone TEXT,
    address TEXT,
    city TEXT,
    postal_code TEXT,
    birthdate TEXT,
    joined_at TEXT DEFAULT CURRENT_TIMESTAMP,
    is_active INTEGER DEFAULT 1,
    notes TEXT
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT UNIQUE,
    password_hash TEXT NOT NULL,
    role_id INTEGER NOT NULL,
    member_id INTEGER,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    last_login TEXT,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL
);



CREATE TABLE IF NOT EXISTS contributions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    member_id INTEGER NOT NULL,
    amount_cents INTEGER NOT NULL,           
    currency TEXT NOT NULL DEFAULT 'EUR',
    paid_at TEXT DEFAULT CURRENT_TIMESTAMP,
    method TEXT,                             
    reference TEXT,
    notes TEXT,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);



CREATE TABLE IF NOT EXISTS missions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    location TEXT,
    start_at TEXT,
    end_at TEXT,
    capacity INTEGER,                        
    budget_cents INTEGER DEFAULT 0,
    created_by INTEGER,                      
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);



CREATE TABLE IF NOT EXISTS mission_participants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    mission_id INTEGER NOT NULL,
    member_id INTEGER NOT NULL,
    role TEXT,                               
    status TEXT DEFAULT 'registered',        
    registered_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mission_id) REFERENCES missions(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    UNIQUE(mission_id, member_id)
);



CREATE TABLE IF NOT EXISTS documents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL,                  
    original_name TEXT,                      
    mime_type TEXT,
    size_bytes INTEGER,
    uploader_id INTEGER,                     
    uploaded_at TEXT DEFAULT CURRENT_TIMESTAMP,
    path TEXT NOT NULL,                      
    description TEXT,
    FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE SET NULL
);



CREATE TABLE IF NOT EXISTS mission_documents (
    mission_id INTEGER NOT NULL,
    document_id INTEGER NOT NULL,
    PRIMARY KEY (mission_id, document_id),
    FOREIGN KEY (mission_id) REFERENCES missions(id) ON DELETE CASCADE,
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS articles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    content TEXT,
    author_id INTEGER,
    status TEXT DEFAULT 'draft',             
    published_at TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);



CREATE TABLE IF NOT EXISTS article_media (
    article_id INTEGER NOT NULL,
    document_id INTEGER NOT NULL,
    PRIMARY KEY (article_id, document_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS partners (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    contact TEXT,
    email TEXT,
    phone TEXT,
    address TEXT,
    website TEXT,
    notes TEXT,
    added_at TEXT DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS subsidies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    partner_id INTEGER,                      
    title TEXT NOT NULL,
    amount_cents INTEGER NOT NULL,
    currency TEXT NOT NULL DEFAULT 'EUR',
    awarded_at TEXT,
    conditions TEXT,
    notes TEXT,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE SET NULL
);



CREATE TABLE IF NOT EXISTS donors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    contact TEXT,
    email TEXT,
    notes TEXT,
    added_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS donations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    donor_id INTEGER NOT NULL,
    amount_cents INTEGER NOT NULL,
    currency TEXT NOT NULL DEFAULT 'EUR',
    donated_at TEXT DEFAULT CURRENT_TIMESTAMP,
    method TEXT,
    reference TEXT,
    notes TEXT,
    FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
);



CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity TEXT,                              
    entity_id INTEGER,
    action TEXT,                              
    performed_by INTEGER,                      
    details TEXT,                              
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL
);








