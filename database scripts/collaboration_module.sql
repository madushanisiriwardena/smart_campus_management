
-- Creating table for collaboration groups
CREATE TABLE collaboration_groups (
    group_id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(255) NOT NULL,
	group_description VARCHAR(255) NOT NULL,
    created_by INT NOT NULL,
    group_type ENUM('private', 'public') NOT NULL DEFAULT 'private',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES user_tb(id) ON DELETE CASCADE
);

-- Creating table for group members
CREATE TABLE group_members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('member', 'admin') NOT NULL DEFAULT 'member',
    permission_level ENUM('view', 'edit', 'delete') NOT NULL DEFAULT 'view',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES collaboration_groups(group_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user_tb(id) ON DELETE CASCADE
);

	CREATE TABLE chats (
		chat_id INT AUTO_INCREMENT PRIMARY KEY,
		sender_id INT NOT NULL,
		receiver_id INT NULL, -- Null if it's a group chat
		group_id INT NULL, -- Null if it's an individual chat
		message_text TEXT NOT NULL,
		file_attachment VARCHAR(255) NULL,
		sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		FOREIGN KEY (sender_id) REFERENCES user_tb(id) ON DELETE CASCADE,
		FOREIGN KEY (receiver_id) REFERENCES user_tb(id) ON DELETE CASCADE,
		FOREIGN KEY (group_id) REFERENCES collaboration_groups(group_id) ON DELETE CASCADE
	);

-- Creating table for shared files
CREATE TABLE shared_files (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    uploaded_by INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    visibility_scope ENUM('public', 'private') NOT NULL DEFAULT 'private',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES user_tb(id) ON DELETE CASCADE
);

-- Creating table for file access control
CREATE TABLE file_access_control (
    access_id INT AUTO_INCREMENT PRIMARY KEY,
    file_id INT NOT NULL,
    user_id INT NULL,
    group_id INT NULL,
    permission ENUM('view', 'edit', 'delete') NOT NULL DEFAULT 'view',
    granted_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (file_id) REFERENCES shared_files(file_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user_tb(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES collaboration_groups(group_id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES user_tb(id) ON DELETE CASCADE
);

-- Creating table for task management
CREATE TABLE task_management (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    assigned_by INT NOT NULL,
    task_name VARCHAR(255) NOT NULL,
    task_description TEXT NOT NULL,
    status ENUM('To-Do', 'In Progress', 'Completed') NOT NULL DEFAULT 'To-Do',
    due_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES collaboration_groups(group_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES user_tb(id) ON DELETE CASCADE
);
