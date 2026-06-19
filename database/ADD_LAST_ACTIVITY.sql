-- Add last_activity column untuk online status tracking
ALTER TABLE users 
ADD COLUMN last_activity DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Set semua user last_activity ke now
UPDATE users SET last_activity = NOW();
