-- Migration: Add selected_plan column to contact_messages table
-- Run this if you already have an existing database

-- Add the selected_plan column
ALTER TABLE contact_messages 
ADD COLUMN selected_plan VARCHAR(100) NULL AFTER message;

-- Add index for better query performance
ALTER TABLE contact_messages 
ADD INDEX idx_plan (selected_plan);

-- Verify the change
DESCRIBE contact_messages;
