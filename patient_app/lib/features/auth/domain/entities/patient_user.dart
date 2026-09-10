import 'package:equatable/equatable.dart';

class PatientUser extends Equatable {
  final int id;
  final String uuid;
  final String fullName;
  final String phone;
  final String? email;
  final String? token;

  const PatientUser({
    required this.id,
    required this.uuid,
    required this.fullName,
    required this.phone,
    this.email,
    this.token,
  });

  @override
  List<Object?> get props => [id, uuid, fullName, phone, email, token];
}
