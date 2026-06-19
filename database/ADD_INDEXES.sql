-- Database Performance Optimization
-- Add indexes untuk speed up queries
-- Note: Jika index sudah ada, skip baris yang error

-- Index untuk ORDER BY created_at (sering dipakai di carousel, posts)
CREATE INDEX idx_posts_created ON community_posts(created_at);
CREATE INDEX idx_global_posts_created ON global_posts(created_at);
CREATE INDEX idx_events_date ON events(tanggal);

-- Index untuk WHERE conditions
CREATE INDEX idx_posts_visibility ON community_posts(visibility);
CREATE INDEX idx_posts_community ON community_posts(id_community);
CREATE INDEX idx_posts_user ON community_posts(id_user);

-- Index untuk JOIN operations
CREATE INDEX idx_members_user ON community_members(id_user);
CREATE INDEX idx_members_community ON community_members(id_community);
CREATE INDEX idx_participants_user ON event_participants(id_user);
CREATE INDEX idx_participants_event ON event_participants(id_event);

-- Composite index untuk combined WHERE clauses
CREATE INDEX idx_posts_community_visibility ON community_posts(id_community, visibility);

-- Speed up user lookups
CREATE INDEX idx_users_email ON users(email);
