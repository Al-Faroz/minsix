-- PHASE 10 — Branding normalization
-- Safe for existing database after FINAL-D2.
-- No schema changes. Only replaces the exact text "MIN 6 Jember" with "MIN 6 JEMBER".

SET NAMES utf8mb4;

UPDATE site_settings
SET setting_value = REPLACE(setting_value, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE setting_value LIKE '%MIN 6 Jember%';

UPDATE media
SET
    alt_text = REPLACE(alt_text, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    caption = REPLACE(caption, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE
    alt_text LIKE '%MIN 6 Jember%'
    OR caption LIKE '%MIN 6 Jember%';

UPDATE homepage_sections
SET
    eyebrow = REPLACE(eyebrow, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    title = REPLACE(title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    subtitle = REPLACE(subtitle, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    body = REPLACE(body, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    cta_label = REPLACE(cta_label, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    secondary_cta_label = REPLACE(secondary_cta_label, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE
    eyebrow LIKE '%MIN 6 Jember%'
    OR title LIKE '%MIN 6 Jember%'
    OR subtitle LIKE '%MIN 6 Jember%'
    OR body LIKE '%MIN 6 Jember%'
    OR cta_label LIKE '%MIN 6 Jember%'
    OR secondary_cta_label LIKE '%MIN 6 Jember%';

UPDATE profile_sections
SET
    title = REPLACE(title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    body = REPLACE(body, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE title LIKE '%MIN 6 Jember%' OR body LIKE '%MIN 6 Jember%';

UPDATE programs
SET
    name = REPLACE(name, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    summary = REPLACE(summary, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    content = REPLACE(content, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE
    name LIKE '%MIN 6 Jember%'
    OR summary LIKE '%MIN 6 Jember%'
    OR content LIKE '%MIN 6 Jember%';

UPDATE gtk
SET short_bio = REPLACE(short_bio, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE short_bio LIKE '%MIN 6 Jember%';

UPDATE news
SET
    title = REPLACE(title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    summary = REPLACE(summary, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    content = REPLACE(content, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    meta_title = REPLACE(meta_title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    meta_description = REPLACE(meta_description, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE
    title LIKE '%MIN 6 Jember%'
    OR summary LIKE '%MIN 6 Jember%'
    OR content LIKE '%MIN 6 Jember%'
    OR meta_title LIKE '%MIN 6 Jember%'
    OR meta_description LIKE '%MIN 6 Jember%';

UPDATE events
SET
    title = REPLACE(title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    summary = REPLACE(summary, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    description = REPLACE(description, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    location = REPLACE(location, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE
    title LIKE '%MIN 6 Jember%'
    OR summary LIKE '%MIN 6 Jember%'
    OR description LIKE '%MIN 6 Jember%'
    OR location LIKE '%MIN 6 Jember%';

UPDATE achievements
SET
    title = REPLACE(title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    participant_name = REPLACE(participant_name, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    organizer = REPLACE(organizer, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    summary = REPLACE(summary, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    content = REPLACE(content, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    meta_title = REPLACE(meta_title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    meta_description = REPLACE(meta_description, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE
    title LIKE '%MIN 6 Jember%'
    OR participant_name LIKE '%MIN 6 Jember%'
    OR organizer LIKE '%MIN 6 Jember%'
    OR summary LIKE '%MIN 6 Jember%'
    OR content LIKE '%MIN 6 Jember%'
    OR meta_title LIKE '%MIN 6 Jember%'
    OR meta_description LIKE '%MIN 6 Jember%';

UPDATE galleries
SET
    title = REPLACE(title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    description = REPLACE(description, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE title LIKE '%MIN 6 Jember%' OR description LIKE '%MIN 6 Jember%';

UPDATE gallery_items
SET caption = REPLACE(caption, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE caption LIKE '%MIN 6 Jember%';

UPDATE spmb_periods
SET
    title = REPLACE(title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    summary = REPLACE(summary, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    content = REPLACE(content, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    contact_name = REPLACE(contact_name, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE
    title LIKE '%MIN 6 Jember%'
    OR summary LIKE '%MIN 6 Jember%'
    OR content LIKE '%MIN 6 Jember%'
    OR contact_name LIKE '%MIN 6 Jember%';

UPDATE spmb_requirements
SET requirement_text = REPLACE(requirement_text, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE requirement_text LIKE '%MIN 6 Jember%';

UPDATE spmb_faq
SET
    question = REPLACE(question, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    answer = REPLACE(answer, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE question LIKE '%MIN 6 Jember%' OR answer LIKE '%MIN 6 Jember%';

UPDATE spmb_steps
SET
    title = REPLACE(title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    description = REPLACE(description, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE title LIKE '%MIN 6 Jember%' OR description LIKE '%MIN 6 Jember%';

UPDATE spmb_highlights
SET
    title = REPLACE(title, 'MIN 6 Jember', 'MIN 6 JEMBER'),
    description = REPLACE(description, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE title LIKE '%MIN 6 Jember%' OR description LIKE '%MIN 6 Jember%';

UPDATE instagram_posts
SET caption = REPLACE(caption, 'MIN 6 Jember', 'MIN 6 JEMBER')
WHERE caption LIKE '%MIN 6 Jember%';
