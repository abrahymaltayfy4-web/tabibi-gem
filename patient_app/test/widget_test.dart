import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:patient_app/core/network/api_client.dart';
import 'package:patient_app/core/security/secure_storage_service.dart';
import 'package:patient_app/main.dart';

void main() {
  testWidgets('App initializes correctly', (WidgetTester tester) async {
    final storage = SecureStorageService();
    final apiClient = ApiClient(dio: Dio(), secureStorage: storage);

    await tester.pumpWidget(TabibiPatientApp(
      apiClient: apiClient,
      secureStorage: storage,
    ));

    expect(find.byType(TabibiPatientApp), findsOneWidget);
  });
}
