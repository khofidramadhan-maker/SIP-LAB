<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'mahasiswa', 'staff', 'kepala_lab'])->default('mahasiswa');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'student_number')) {
                $table->string('student_number')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'program_study')) {
                $table->string('program_study')->nullable();
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            }
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nim')->nullable();
            $table->string('faculty')->nullable();
            $table->string('study_program')->nullable();
            $table->string('advisor_name')->nullable();
            $table->string('advisor_nip')->nullable();
            $table->string('academic_year')->nullable();
            $table->timestamps();
            $table->index('nim');
        });

        Schema::dropIfExists('notifications');
        Schema::dropIfExists('penalty_policies');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('return_records');
        Schema::dropIfExists('handovers');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('verifications');
        Schema::dropIfExists('booking_documents');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('lab_rooms');

        Schema::create('lab_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('location');
            $table->integer('capacity');
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'maintenance', 'busy'])->default('available');
            $table->time('open_time')->default('08:00:00');
            $table->time('close_time')->default('17:00:00');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_room_id')->nullable()->constrained('lab_rooms')->nullOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('condition')->default('baik');
            $table->enum('status', ['available', 'borrowed', 'maintenance', 'missing'])->default('available');
            $table->integer('quantity')->default(1);
            $table->string('unit')->default('unit');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_room_id')->nullable()->constrained('lab_rooms')->nullOnDelete();
            $table->string('title');
            $table->text('purpose');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('request_type')->default('lab_usage');
            $table->string('document_recommendation')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_nip')->nullable();
            $table->enum('status', ['Draft', 'Menunggu Verifikasi', 'Perlu Revisi', 'Ditolak', 'Menunggu Persetujuan Kepala Lab', 'Disetujui', 'Siap Digunakan', 'Sedang Dipinjam', 'Menunggu Pemeriksaan Pengembalian', 'Selesai'])->default('Draft');
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->index(['lab_room_id', 'start_date', 'end_date']);
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->string('file_name');
            $table->string('file_path');
            $table->timestamps();
        });

        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['approved', 'rejected', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['approved', 'rejected', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('handover_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('handover_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('return_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('returned_at')->nullable();
            $table->enum('condition_status', ['baik', 'rusak', 'hilang', 'terlambat'])->default('baik');
            $table->integer('late_hours')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->string('severity')->default('medium');
            $table->text('description');
            $table->enum('status', ['open', 'resolved'])->default('open');
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('penalty_policies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('daily_fee', 12, 2)->default(0);
            $table->decimal('max_fee', 12, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('info');
            $table->string('title');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index('read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('penalty_policies');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('return_records');
        Schema::dropIfExists('handovers');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('verifications');
        Schema::dropIfExists('booking_documents');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('lab_rooms');
        Schema::dropIfExists('students');
    }
};
